<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ModerationEvent;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReportModerationController extends Controller
{
    public function update(Request $request, Report $report): RedirectResponse
    {
        abort_unless($request->user()?->role === UserRole::ADMIN, 403);
        $validated = $request->validate([
            'status' => ['required', Rule::in(['resolved', 'dismissed'])],
            'resolution' => ['required', 'string', 'max:1000'],
            'block_listing' => ['boolean'],
        ]);

        DB::transaction(function () use ($request, $report, $validated) {
            $report->update([
                'status' => $validated['status'],
                'resolver_id' => $request->user()->id,
                'resolution' => $validated['resolution'],
            ]);

            if ($request->boolean('block_listing')) {
                $report->listing()->update(['moderation_status' => 'blocked']);
                ModerationEvent::create([
                    'actor_id' => $request->user()->id,
                    'listing_id' => $report->listing_id,
                    'action' => 'blocked',
                    'reason' => $validated['resolution'],
                    'created_at' => now(),
                ]);
            }
        });

        Cache::forget('home.featured-listings');

        return back()->with('success', 'Shikoyat bo‘yicha qaror saqlandi.');
    }
}
