<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Models\Listing;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RequestController extends Controller
{
    public function create(Listing $listing)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (! $listing->isPubliclyVisible()) {
            abort(404);
        }

        abort_if($listing->owner_user_id === auth()->id(), 403, 'O‘z e’loningizga murojaat yubora olmaysiz.');

        return Inertia::render('CreateRequest', [
            'listing' => $listing->load('owner', 'district'),
        ]);
    }

    public function store(Request $request, Listing $listing)
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
            abort_if($lockedListing->owner_user_id === auth()->id(), 403, 'O‘z e’loningizga murojaat yubora olmaysiz.');

            if ($lockedListing->free_places !== null && ($validated['occupants_count'] ?? 1) > $lockedListing->free_places) {
                abort(422, 'Yashovchilar soni bo‘sh o‘rinlardan ko‘p.');
            }

            $retried = RequestModel::where('idempotency_key', $validated['idempotency_key'])->first();
            if ($retried) {
                return $retried;
            }

            $existing = RequestModel::query()
                ->where('requester_id', auth()->id())
                ->where('listing_id', $lockedListing->id)
                ->whereIn('status', [RequestStatus::NEW->value, RequestStatus::ALTERNATIVE_PROPOSED->value, RequestStatus::ACCEPTED->value])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                abort(409, 'Sizning faol murojaatingiz mavjud.');
            }

            $created = RequestModel::create([
                ...$validated,
                'requester_id' => auth()->id(),
                'recipient_id' => $lockedListing->owner_user_id,
                'listing_id' => $lockedListing->id,
                'status' => RequestStatus::NEW,
            ]);

            $created->events()->create([
                'actor_id' => auth()->id(),
                'from_status' => 'created',
                'to_status' => RequestStatus::NEW->value,
                'proposed_at' => $created->proposed_at,
            ]);

            return $created;
        });

        return redirect()->route('account.requests')->with('success', 'Murojaat yuborildi. Javobni kuting.');
    }

    public function updateStatus(RequestModel $request, Request $formRequest)
    {
        $this->authorize('update', $request);

        $validated = $formRequest->validate([
            'status' => ['required', 'string', 'in:accepted,alternative_proposed,rejected,cancelled,completed'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'proposed_at' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $newStatus = RequestStatus::from($validated['status']);
        $current = $request->status;
        $isRecipient = auth()->id() === $request->recipient_id;

        $allowedTransitions = $isRecipient
            ? [
                RequestStatus::NEW->value => [RequestStatus::ACCEPTED, RequestStatus::ALTERNATIVE_PROPOSED, RequestStatus::REJECTED],
                RequestStatus::ALTERNATIVE_PROPOSED->value => [RequestStatus::REJECTED],
                RequestStatus::ACCEPTED->value => [RequestStatus::COMPLETED, RequestStatus::CANCELLED],
            ]
            : [
                RequestStatus::NEW->value => [RequestStatus::CANCELLED],
                RequestStatus::ALTERNATIVE_PROPOSED->value => [RequestStatus::ACCEPTED, RequestStatus::CANCELLED],
                RequestStatus::ACCEPTED->value => [RequestStatus::CANCELLED],
            ];

        if (! in_array($newStatus, $allowedTransitions[$current->value] ?? [], true)) {
            abort(403);
        }

        DB::transaction(function () use ($request, $current, $newStatus, $validated) {
            $request->update([
                'status' => $newStatus,
                'proposed_at' => $validated['proposed_at'] ?? $request->proposed_at,
            ]);

            $request->events()->create([
                'from_status' => $current->value,
                'to_status' => $newStatus->value,
                'actor_id' => auth()->id(),
                'proposed_at' => $validated['proposed_at'] ?? null,
                'comment' => $validated['comment'] ?? null,
            ]);
        });

        return back()->with('success', 'Status o‘zgartirildi.');
    }
}
