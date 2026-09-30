<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Listing;
use App\Services\CostCalculationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::query()
            ->where('moderation_status', 'approved')
            ->where('availability_status', 'available')
            ->whereNull('archived_at')
            ->where(function ($q) {
                $q->whereNull('confirmed_at')
                    ->orWhereDate('confirmed_at', '>=', now()->subDays(14));
            });

        // Deal type filter
        if ($request->has('deal_type')) {
            $query->where('deal_type', $request->get('deal_type'));
        }

        // Rental unit filter
        if ($request->has('rental_unit') && $request->get('rental_unit') !== 'all') {
            $query->where('rental_unit', $request->get('rental_unit'));
        }

        // Students allowed filter
        if ($request->get('students_allowed') === 'yes') {
            $query->where('students_allowed', 'yes');
        }

        // District filter
        if ($request->has('district_id')) {
            $query->where('district_id', $request->get('district_id'));
        }

        // Price filter
        if ($request->has('price_min') && $request->get('price_min')) {
            $query->where('price', '>=', $request->get('price_min'));
        }

        if ($request->has('price_max') && $request->get('price_max')) {
            $query->where('price', '<=', $request->get('price_max'));
        }

        // Currency filter
        if ($request->has('currency')) {
            $query->where('currency', $request->get('currency'));
        }

        // Capacity/Free places filter
        if ($request->has('free_places_min') && $request->get('free_places_min')) {
            $query->where('free_places', '>=', $request->get('free_places_min'));
        }

        // Date filter
        if ($request->has('available_from')) {
            $query->whereDate('available_from', '<=', $request->get('available_from'));
        }

        // Search filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location_text', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'confirmed_at');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'date') {
            $query->orderBy('published_at', 'desc');
        } else {
            $query->orderByDesc('confirmed_at')->orderByDesc('id');
        }

        $listings = $query->with('media', 'amenities', 'owner', 'district')
            ->paginate(12)
            ->appends($request->query());

        $districts = District::where('active', true)->pluck('name_uz', 'id');

        return Inertia::render('Catalog', [
            'listings' => $listings,
            'districts' => $districts,
            'filters' => $request->all(),
        ]);
    }

    public function show(Listing $listing, CostCalculationService $costs)
    {
        if (! $listing->isPubliclyVisible()
            && (! auth()->check() || auth()->id() !== $listing->owner_user_id)) {
            abort(404);
        }

        $listing->load('media', 'amenities', 'owner', 'district', 'project');

        return Inertia::render('ListingDetail', [
            'listing' => $listing,
            'costs' => $listing->deal_type->value === 'rent' ? $costs->calculate($listing) : null,
        ]);
    }
}
