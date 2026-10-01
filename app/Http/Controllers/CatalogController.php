<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogFilterRequest;
use App\Models\Amenity;
use App\Models\District;
use App\Models\Listing;
use App\Services\CostCalculationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(CatalogFilterRequest $request): Response
    {
        $filters = $request->validated();
        $query = Listing::query()
            ->where('moderation_status', 'approved')->where('availability_status', 'available')->whereNull('archived_at')
            ->where(fn ($q) => $q->whereNull('confirmed_at')->orWhereDate('confirmed_at', '>=', now()->subDays(14)));

        $simpleFilters = ['deal_type', 'rental_unit', 'district_id', 'currency', 'property_type', 'author_type'];
        foreach ($simpleFilters as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (($filters['students_allowed'] ?? null) === 'yes') {
            $query->where('students_allowed', 'yes');
        }
        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }
        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }
        if (! empty($filters['free_places_min'])) {
            $query->where('free_places', '>=', $filters['free_places_min']);
        }
        if (! empty($filters['rooms_min'])) {
            $query->where('rooms', '>=', $filters['rooms_min']);
        }
        if (! empty($filters['available_from'])) {
            $query->whereNotNull('available_from')->whereDate('available_from', '<=', $filters['available_from']);
        }
        foreach ($filters['amenities'] ?? [] as $code) {
            $query->whereHas('amenities', fn ($amenityQuery) => $amenityQuery->where('code', $code));
        }
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")->orWhere('location_text', 'like', "%{$search}%"));
        }
        if (! empty($filters['locality'])) {
            $query->where('location_text', 'like', '%'.$filters['locality'].'%');
        }

        $sort = $filters['sort'] ?? 'confirmed_at';
        $canSortByPrice = ! empty($filters['currency']) && (($filters['deal_type'] ?? null) === 'sale' || ! empty($filters['rental_unit']));
        if (in_array($sort, ['price_asc', 'price_desc'], true) && ! $canSortByPrice) {
            $sort = 'confirmed_at';
        }
        match ($sort) {
            'price_asc' => $query->orderByRaw('price IS NULL')->orderBy('price'),
            'price_desc' => $query->orderByRaw('price IS NULL')->orderByDesc('price'),
            'date' => $query->orderByDesc('published_at'),
            default => $query->orderByDesc('confirmed_at')->orderByDesc('id'),
        };

        return Inertia::render('Catalog', [
            'listings' => $query->with('media', 'amenities', 'owner', 'district')->paginate(12)->appends($filters),
            'districts' => Cache::remember('reference.district-map', now()->addHour(), fn () => District::where('active', true)->orderBy('name_uz')->pluck('name_uz', 'id')),
            'amenities' => Cache::remember('reference.catalog-amenities', now()->addHour(), fn () => Amenity::whereIn('code', ['wifi', 'furniture'])->pluck('name_uz', 'code')),
            'filters' => $filters,
            'priceSortAvailable' => $canSortByPrice,
            'localities' => config('locations.andijan_city_areas'),
        ]);
    }

    public function show(Listing $listing, CostCalculationService $costs): Response
    {
        if (! $listing->isPubliclyVisible() && (! Auth::check() || Auth::id() !== $listing->owner_user_id)) {
            abort(404);
        }
        $listing->load('media', 'amenities', 'owner', 'district', 'project');

        return Inertia::render('ListingDetail', [
            'listing' => $listing,
            'costs' => $listing->deal_type->value === 'rent' ? $costs->calculate($listing) : null,
        ]);
    }
}
