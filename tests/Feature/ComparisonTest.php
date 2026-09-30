<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_visible_listings_can_be_compared_with_calculated_costs(): void
    {
        $owner = User::factory()->create();
        $district = District::create(['name_uz' => 'Andijon', 'active' => true]);
        $listings = collect([700000, 800000])->map(fn ($price) => Listing::create([
            'owner_user_id' => $owner->id, 'district_id' => $district->id,
            'deal_type' => 'rent', 'rental_unit' => 'bed', 'property_type' => 'apartment',
            'students_allowed' => 'yes', 'title' => 'Demo '.$price, 'description' => 'Demo',
            'currency' => 'UZS', 'price' => $price, 'price_basis' => 'monthly_unit',
            'utilities_mode' => 'included', 'deposit_mode' => 'none', 'commission_mode' => 'none',
            'moderation_status' => 'approved', 'availability_status' => 'available', 'confirmed_at' => now(),
        ]));

        $this->get('/compare?'.http_build_query(['ids' => $listings->pluck('id')->all()]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Compare')
                ->has('items', 2)
                ->where('compatibility.compatible', true)
                ->where('items.0.costs.monthly_payment', 700000));
    }
}
