<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Listing;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $confirmationDays = (int) PlatformSetting::number('listing_confirmation_days', 7);

        return Inertia::render('Home', [
            'featuredListings' => Cache::remember('home.featured-listings', now()->addMinutes(10), fn () => Listing::query()
                ->with(['district', 'media'])
                ->where('moderation_status', 'approved')
                ->where('availability_status', 'available')
                ->whereNull('archived_at')
                ->where(fn ($query) => $query->whereNull('confirmed_at')->orWhere('confirmed_at', '>=', now()->subDays($confirmationDays)))
                ->latest('confirmed_at')
                ->limit(6)
                ->get()),
            'districts' => Cache::remember('reference.districts', now()->addHour(), fn () => District::query()->where('active', true)->orderBy('name_uz')->get(['id', 'name_uz'])),
            'localities' => config('locations.andijan_city_areas'),
        ]);
    }
}
