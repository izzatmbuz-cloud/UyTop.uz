<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function store(Request $request, Listing $listing): RedirectResponse
    {
        abort_unless($listing->isPubliclyVisible(), 404);
        abort_if($listing->owner_user_id === $request->user()->id, 422, 'O‘z e’loningiz ustidan shikoyat yubora olmaysiz.');

        $validated = $request->validate([
            'reason' => ['required', Rule::in(['incorrect', 'unavailable', 'fraud', 'duplicate', 'other'])],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Report::firstOrCreate(
            ['listing_id' => $listing->id, 'reporter_id' => $request->user()->id],
            [...$validated, 'status' => 'new'],
        );

        return back()->with('success', 'Shikoyat yuborildi. Administrator uni ko‘rib chiqadi.');
    }
}
