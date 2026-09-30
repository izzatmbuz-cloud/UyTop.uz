<?php

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Http\Requests\SaveListingRequest;
use App\Models\Amenity;
use App\Models\District;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ListingController extends Controller
{
    public function index(): Response
    {
        $listings = auth()->user()->listings()
            ->with(['district', 'media', 'moderationEvents' => fn ($query) => $query->latest('created_at')->limit(1)])
            ->latest()
            ->get();

        return Inertia::render('Account/Listings', compact('listings'));
    }

    public function create(): Response
    {
        Gate::authorize('create', Listing::class);

        return Inertia::render('Account/ListingForm', $this->formData());
    }

    public function store(SaveListingRequest $request): RedirectResponse
    {
        Gate::authorize('create', Listing::class);

        $listing = DB::transaction(function () use ($request) {
            $data = $this->listingData($request);
            $data['owner_user_id'] = $request->user()->id;
            $data['moderation_status'] = $request->boolean('submit_for_moderation') ? 'pending' : 'draft';
            $data['availability_status'] = 'available';
            $data['author_type'] = 'owner';
            $data['source_type'] = 'direct';

            $listing = Listing::create($data);
            $listing->amenities()->sync($request->input('amenity_ids', []));

            return $listing;
        });

        $this->forgetPublicCache();

        return redirect()->route('account.listings')->with('success', $listing->moderation_status === ModerationStatus::PENDING
            ? 'E’lon moderatsiyaga yuborildi.'
            : 'Qoralama saqlandi.');
    }

    public function edit(Listing $listing): Response
    {
        Gate::authorize('update', $listing);
        $listing->load('amenities');

        return Inertia::render('Account/ListingForm', [
            ...$this->formData(),
            'listing' => $listing,
        ]);
    }

    public function update(SaveListingRequest $request, Listing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);

        DB::transaction(function () use ($request, $listing) {
            $data = $this->listingData($request);
            $data['moderation_status'] = $request->boolean('submit_for_moderation') ? 'pending' : 'draft';
            $data['published_at'] = null;
            $listing->update($data);
            $listing->amenities()->sync($request->input('amenity_ids', []));
        });

        $this->forgetPublicCache();

        return redirect()->route('account.listings')->with('success', 'E’lon yangilandi.');
    }

    public function archive(Listing $listing): RedirectResponse
    {
        Gate::authorize('archive', $listing);
        $listing->update(['archived_at' => now(), 'availability_status' => 'withdrawn']);
        $this->forgetPublicCache();

        return back()->with('success', 'E’lon arxivlandi.');
    }

    public function confirm(Listing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);
        abort_if($listing->moderation_status === ModerationStatus::BLOCKED, 409, 'Bloklangan e’lonni administrator tekshirishi kerak.');
        abort_if($listing->archived_at !== null, 409, 'Arxivlangan e’lonni tasdiqlab bo‘lmaydi.');

        $listing->update([
            'confirmed_at' => now(),
            'availability_status' => 'available',
        ]);
        $this->forgetPublicCache();

        return back()->with('success', 'E’lon dolzarbligi tasdiqlandi.');
    }

    public function updateAvailability(Request $request, Listing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);
        $terminalStatus = $listing->deal_type->value === 'sale' ? 'sold' : 'rented';
        $validated = $request->validate([
            'status' => ['required', Rule::in(['available', $terminalStatus, 'withdrawn'])],
        ]);

        abort_if($validated['status'] === 'available' && $listing->moderation_status === ModerationStatus::BLOCKED, 409, 'Bloklangan e’lonni qayta faollashtirib bo‘lmaydi.');

        $listing->update([
            'availability_status' => $validated['status'],
            'confirmed_at' => $validated['status'] === 'available' ? now() : $listing->confirmed_at,
        ]);
        $this->forgetPublicCache();

        return back()->with('success', 'Mavjudlik holati yangilandi.');
    }

    private function formData(): array
    {
        return [
            'districts' => Cache::remember('reference.districts', now()->addHour(), fn () => District::where('active', true)->orderBy('name_uz')->get(['id', 'name_uz'])),
            'amenities' => Cache::remember('reference.amenities', now()->addHour(), fn () => Amenity::orderBy('name_uz')->get(['id', 'code', 'name_uz'])),
        ];
    }

    private function listingData(SaveListingRequest $request): array
    {
        $data = Arr::except($request->validated(), ['amenity_ids', 'submit_for_moderation']);
        if ($data['deal_type'] === 'sale') {
            $data['rental_unit'] = null;
            $data['students_allowed'] = null;
        }

        return $data;
    }

    private function forgetPublicCache(): void
    {
        Cache::forget('home.featured-listings');
    }
}
