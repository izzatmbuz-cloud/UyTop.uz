<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Models\Commission;
use App\Models\Listing;
use App\Models\PlatformSetting;
use App\Models\Request as RequestModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class RequestController extends Controller
{
    public function create(Listing $listing): Response|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! $listing->isPubliclyVisible()) {
            abort(404);
        }

        abort_if($listing->owner_user_id === Auth::id(), 403, 'O‘z e’loningizga murojaat yubora olmaysiz.');

        return Inertia::render('CreateRequest', [
            'listing' => $listing->load('owner', 'district'),
        ]);
    }

    public function store(Request $request, Listing $listing): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\-\s]{7,30}$/'],
            'proposed_at' => 'nullable|date|after_or_equal:today',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i',
            'occupants_count' => 'nullable|integer|min:1',
            'message' => 'nullable|string|max:1000',
            'idempotency_key' => 'required|uuid',
        ]);

        $newRequest = DB::transaction(function () use ($validated, $listing) {
            $lockedListing = Listing::query()->lockForUpdate()->findOrFail($listing->id);

            abort_unless($lockedListing->isPubliclyVisible(), 409, 'E’lon hozir mavjud emas.');
            abort_if($lockedListing->owner_user_id === Auth::id(), 403, 'O‘z e’loningizga murojaat yubora olmaysiz.');

            if ($lockedListing->free_places !== null && ($validated['occupants_count'] ?? 1) > $lockedListing->free_places) {
                abort(422, 'Yashovchilar soni bo‘sh o‘rinlardan ko‘p.');
            }

            $retried = RequestModel::where('idempotency_key', $validated['idempotency_key'])->first();
            if ($retried) {
                return $retried;
            }

            $existing = RequestModel::query()
                ->where('requester_id', Auth::id())
                ->where('listing_id', $lockedListing->id)
                ->whereIn('status', [RequestStatus::NEW->value, RequestStatus::ALTERNATIVE_PROPOSED->value, RequestStatus::ACCEPTED->value])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                abort(409, 'Sizning faol murojaatingiz mavjud.');
            }

            $created = RequestModel::create([
                ...$validated,
                'requester_id' => Auth::id(),
                'recipient_id' => $lockedListing->owner_user_id,
                'listing_id' => $lockedListing->id,
                'status' => RequestStatus::NEW,
            ]);

            $created->events()->create([
                'actor_id' => Auth::id(),
                'from_status' => 'created',
                'to_status' => RequestStatus::NEW->value,
                'proposed_at' => $created->proposed_at,
            ]);

            return $created;
        });

        return redirect()->route('account.requests')->with('success', 'Murojaat yuborildi. Javobni kuting.');
    }

    public function updateStatus(RequestModel $request, Request $formRequest): RedirectResponse
    {
        Gate::authorize('update', $request);

        $validated = $formRequest->validate([
            'status' => ['required', 'string', 'in:accepted,alternative_proposed,rejected,cancelled,completed'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'proposed_at' => ['nullable', 'date', 'after_or_equal:today'],
            'time_start' => ['nullable', 'date_format:H:i'],
            'time_end' => ['nullable', 'date_format:H:i', 'after:time_start'],
        ]);

        $newStatus = RequestStatus::from($validated['status']);
        $isRecipient = Auth::id() === $request->recipient_id;
        if ($newStatus === RequestStatus::ALTERNATIVE_PROPOSED && empty($validated['proposed_at'])) {
            return back()->withErrors(['proposed_at' => 'Boshqa vaqt uchun sana majburiy.']);
        }

        DB::transaction(function () use ($request, $isRecipient, $newStatus, $validated) {
            $lockedRequest = RequestModel::query()->lockForUpdate()->findOrFail($request->id);
            $current = $lockedRequest->status;
            $allowedTransitions = $isRecipient
                ? [
                    RequestStatus::NEW->value => [RequestStatus::ACCEPTED, RequestStatus::ALTERNATIVE_PROPOSED, RequestStatus::REJECTED],
                    RequestStatus::ALTERNATIVE_PROPOSED->value => [RequestStatus::REJECTED],
                    RequestStatus::ACCEPTED->value => [RequestStatus::COMPLETED],
                ]
                : [
                    RequestStatus::NEW->value => [RequestStatus::CANCELLED],
                    RequestStatus::ALTERNATIVE_PROPOSED->value => [RequestStatus::ACCEPTED, RequestStatus::CANCELLED],
                    RequestStatus::ACCEPTED->value => [RequestStatus::CANCELLED],
                ];

            abort_unless(in_array($newStatus, $allowedTransitions[$current->value] ?? [], true), 403);

            $lockedRequest->update([
                'status' => $newStatus,
                'proposed_at' => $validated['proposed_at'] ?? $lockedRequest->proposed_at,
                'time_start' => $validated['time_start'] ?? $lockedRequest->time_start,
                'time_end' => $validated['time_end'] ?? $lockedRequest->time_end,
            ]);

            $lockedRequest->events()->create([
                'from_status' => $current->value,
                'to_status' => $newStatus->value,
                'actor_id' => Auth::id(),
                'proposed_at' => $validated['proposed_at'] ?? null,
                'comment' => $validated['comment'] ?? null,
            ]);

            if ($newStatus === RequestStatus::COMPLETED && $lockedRequest->listing_id) {
                $listing = Listing::query()->lockForUpdate()->findOrFail($lockedRequest->listing_id);
                $rate = PlatformSetting::number($listing->deal_type->value === 'sale' ? 'sale_commission_percent' : 'rent_commission_percent', $listing->deal_type->value === 'sale' ? 5 : 20);
                $amount = $listing->price === null ? null : round((float) $listing->price * $rate / 100, 2);

                Commission::updateOrCreate(['request_id' => $lockedRequest->id], [
                    'listing_id' => $listing->id,
                    'payer_user_id' => $listing->owner_user_id,
                    'deal_type' => $listing->deal_type->value,
                    'rate_percent' => $rate,
                    'deal_amount' => $listing->price,
                    'commission_amount' => $amount,
                    'currency' => $listing->currency,
                    'status' => 'pending',
                ]);

                $listing->update(['availability_status' => $listing->deal_type->value === 'sale' ? 'sold' : 'rented']);
                Cache::forget('home.featured-listings');
            }
        });

        return back()->with('success', 'Status o‘zgartirildi.');
    }
}
