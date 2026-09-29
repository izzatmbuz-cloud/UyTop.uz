<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use App\Models\Listing;
use App\Models\Project;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    public function create(Listing $listing)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if ($listing->moderation_status !== 'approved' || $listing->availability_status !== 'available') {
            abort(404);
        }

        return Inertia::render('CreateRequest', [
            'listing' => $listing->load('owner', 'district'),
        ]);
    }

    public function store(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'proposed_at' => 'nullable|date',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i',
            'occupants_count' => 'nullable|integer|min:1',
            'message' => 'nullable|string|max:1000',
        ]);

        $existingRequest = RequestModel::where('requester_id', auth()->id())
            ->where('listing_id', $listing->id)
            ->whereIn('status', ['new', 'alternative_proposed', 'accepted'])
            ->first();

        if ($existingRequest) {
            return back()->withErrors([
                'request' => 'Sizning faol murojaatingiz mavjud.',
            ]);
        }

        $newRequest = RequestModel::create([
            'requester_id' => auth()->id(),
            'recipient_id' => $listing->owner_user_id,
            'listing_id' => $listing->id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'proposed_at' => $validated['proposed_at'] ?? null,
            'time_start' => $validated['time_start'] ?? null,
            'time_end' => $validated['time_end'] ?? null,
            'occupants_count' => $validated['occupants_count'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'new',
            'idempotency_key' => (string) Str::uuid(),
        ]);

        return redirect()->route('account.requests')->with('success', 'Murojaat yuborildi. Javobni kuting.');
    }

    public function updateStatus(RequestModel $request, Request $formRequest)
    {
        $this->authorize('update', $request);

        $newStatus = $formRequest->get('status');
        $comment = $formRequest->get('comment');

        $allowedTransitions = [
            'new' => ['accepted', 'alternative_proposed', 'rejected'],
            'alternative_proposed' => ['accepted', 'rejected'],
            'accepted' => ['completed', 'cancelled'],
            'rejected' => [],
            'cancelled' => [],
            'completed' => [],
        ];

        if (!in_array($newStatus, $allowedTransitions[$request->status] ?? [])) {
            abort(403);
        }

        $request->status = $newStatus;
        $request->save();

        $request->events()->create([
            'from_status' => $request->getOriginal('status'),
            'to_status' => $newStatus,
            'actor_id' => auth()->id(),
            'comment' => $comment,
        ]);

        return response()->json(['message' => 'Status o\'zgartirildi']);
    }
}
