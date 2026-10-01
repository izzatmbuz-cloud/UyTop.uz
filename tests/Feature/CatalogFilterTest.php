<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_search_parameters_filter_catalog(): void
    {
        $owner = User::factory()->create();
        $district = District::create(['name_uz' => 'Andijon shahri', 'active' => true]);
        foreach ([['Eski shahar kvartirasi', 'Eski shahar'], ['Boshqa uy', 'Yangi shahar']] as [$title, $location]) {
            Listing::create([
                'owner_user_id' => $owner->id, 'district_id' => $district->id, 'deal_type' => 'rent',
                'rental_unit' => 'bed', 'property_type' => 'apartment', 'students_allowed' => 'yes',
                'title' => $title, 'description' => 'Sinov uchun yetarlicha uzun e’lon tavsifi.',
                'location_text' => $location, 'currency' => 'UZS', 'price' => 700000,
                'price_basis' => 'monthly_unit', 'moderation_status' => 'approved',
                'availability_status' => 'available', 'confirmed_at' => now(),
            ]);
        }

        $response = $this->get(route('catalog', ['deal_type' => 'rent', 'rental_unit' => 'bed', 'district_id' => $district->id, 'locality' => 'Eski shahar', 'search' => 'kvartira', 'currency' => 'UZS']));

        $response->assertOk();
        $this->assertSame(1, $response->viewData('page')['props']['listings']['total']);
        $this->assertSame('Eski shahar kvartirasi', $response->viewData('page')['props']['listings']['data'][0]['title']);
    }
}
