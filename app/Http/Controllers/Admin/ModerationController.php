<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Listing;
use App\Models\ModerationEvent;
use App\Models\PlatformSetting;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ModerationController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAdmin($request);

        return Inertia::render('Admin/Moderation', [
            'listings' => Listing::with(['owner:id,name,email', 'district:id,name_uz'])
                ->where('moderation_status', 'pending')
                ->oldest()
                ->paginate(20),
            'reports' => Report::with(['listing:id,title,moderation_status', 'reporter:id,name,email'])
                ->where('status', 'new')
                ->oldest()
                ->get(),
            'commissions' => Commission::with(['listing:id,title', 'payer:id,name,email', 'request:id,requester_id'])
                ->latest()->limit(100)->get(),
            'settings' => [
                'rent_commission_percent' => PlatformSetting::number('rent_commission_percent', 20),
                'sale_commission_percent' => PlatformSetting::number('sale_commission_percent', 5),
                'listing_confirmation_days' => PlatformSetting::number('listing_confirmation_days', 7),
            ],
        ]);
    }

    public function update(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected', 'blocked'])],
            'reason' => ['nullable', 'string', 'max:1000', 'required_if:status,rejected,blocked'],
        ]);

        DB::transaction(function () use ($request, $listing, $validated) {
            $listing->update([
                'moderation_status' => $validated['status'],
                'published_at' => $validated['status'] === 'approved' ? now() : null,
                'confirmed_at' => $validated['status'] === 'approved' ? now() : $listing->confirmed_at,
            ]);
            ModerationEvent::create([
                'actor_id' => $request->user()->id,
                'listing_id' => $listing->id,
                'action' => $validated['status'],
                'reason' => $validated['reason'] ?? null,
                'created_at' => now(),
            ]);
        });

        Cache::forget('home.featured-listings');

        return back()->with('success', 'Moderatsiya qarori saqlandi.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === UserRole::ADMIN, 403);
    }
}
