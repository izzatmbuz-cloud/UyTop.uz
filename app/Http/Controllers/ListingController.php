<?php

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Http\Requests\SaveListingRequest;
use App\Models\Amenity;
use App\Models\District;
use App\Models\Listing;
use App\Models\Media;
use App\Services\ListingImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

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

    public function store(SaveListingRequest $request, ListingImageService $images): RedirectResponse
    {
        Gate::authorize('create', Listing::class);

        $listing = DB::transaction(function () use ($request) {
            $data = $this->listingData($request);
            $data['owner_user_id'] = $request->user()->id;
            $data['moderation_status'] = 'draft';
            $data['availability_status'] = 'available';
            $data['author_type'] = 'owner';
            $data['source_type'] = 'direct';

            $listing = Listing::create($data);
            $listing->amenities()->sync($request->input('amenity_ids', []));

            return $listing;
        });

        try {
            $this->storeImages($request, $listing, $images);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('account.listings.edit', $listing)->with('error', 'Qoralama saqlandi, lekin rasmlardan birini qayta ishlash imkoni bo‘lmadi. Rasmni almashtirib qayta urinib ko‘ring.');
        }

        if ($request->boolean('submit_for_moderation')) {
            $listing->update(['moderation_status' => 'pending']);
        }

        $this->forgetPublicCache();

        return redirect()->route('account.listings')->with('success', $listing->moderation_status === ModerationStatus::PENDING
            ? 'E’lon moderatsiyaga yuborildi.'
            : 'Qoralama saqlandi.');
    }

    public function edit(Listing $listing): Response
    {
        Gate::authorize('update', $listing);
        $listing->load(['amenities', 'media' => fn ($query) => $query->orderBy('sort_order')]);

        return Inertia::render('Account/ListingForm', [
            ...$this->formData(),
            'listing' => $listing,
        ]);
    }

    public function update(SaveListingRequest $request, Listing $listing, ListingImageService $images): RedirectResponse
    {
        Gate::authorize('update', $listing);

        DB::transaction(function () use ($request, $listing) {
            $data = $this->listingData($request);
            $data['moderation_status'] = 'draft';
            $data['published_at'] = null;
            $listing->update($data);
            $listing->amenities()->sync($request->input('amenity_ids', []));
        });

        try {
            $this->storeImages($request, $listing, $images);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'O‘zgarishlar qoralama sifatida saqlandi, lekin rasmni qayta ishlash imkoni bo‘lmadi.');
        }

        if ($request->boolean('submit_for_moderation')) {
            $listing->update(['moderation_status' => 'pending']);
        }

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

    public function destroyImage(Listing $listing, Media $media): RedirectResponse
    {
        Gate::authorize('update', $listing);
        abort_unless($media->listing_id === $listing->id, 404);
        abort_if($listing->media()->count() <= 1 && $listing->moderation_status !== ModerationStatus::DRAFT, 422, 'Faol e’londa kamida bitta rasm qolishi kerak.');

        Storage::disk('public')->delete($media->storage_path);
        $media->delete();

        return back()->with('success', 'Rasm o‘chirildi.');
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
            'localities' => config('locations.andijan_city_areas'),
        ];
    }

    private function listingData(SaveListingRequest $request): array
    {
        $data = Arr::except($request->validated(), ['amenity_ids', 'submit_for_moderation', 'images']);
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

    private function storeImages(SaveListingRequest $request, Listing $listing, ListingImageService $images): void
    {
        $nextOrder = (int) $listing->media()->max('sort_order') + 1;
        foreach ($request->file('images', []) as $index => $image) {
            $listing->media()->create([...$images->store($image), 'sort_order' => $nextOrder + $index]);
        }
    }
}
