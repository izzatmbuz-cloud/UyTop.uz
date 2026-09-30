<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_listing_and_submit_it_for_moderation(): void
    {
        $user = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
        $district = District::create(['name_uz' => 'Andijon shahri', 'active' => true]);

        $response = $this->actingAs($user)->post(route('account.listings.store'), [
            'deal_type' => 'rent',
            'rental_unit' => 'bed',
            'property_type' => 'apartment',
            'students_allowed' => 'yes',
            'district_id' => $district->id,
            'title' => 'Universitet yaqinida qulay joy',
            'description' => 'Talabalar uchun barcha zarur sharoitlar mavjud bo‘lgan joy.',
            'currency' => 'UZS',
            'price' => 700000,
            'price_basis' => 'monthly_unit',
            'utilities_mode' => 'unknown',
            'deposit_mode' => 'none',
            'commission_mode' => 'none',
            'capacity' => 3,
            'free_places' => 2,
            'amenity_ids' => [],
            'submit_for_moderation' => true,
        ]);

        $response->assertRedirect(route('account.listings'));
        $this->assertDatabaseHas('listings', [
            'owner_user_id' => $user->id,
            'moderation_status' => 'pending',
            'title' => 'Universitet yaqinida qulay joy',
        ]);
    }

    public function test_user_cannot_edit_someone_elses_listing(): void
    {
        [$owner, $stranger] = User::factory()->count(2)->create(['role' => 'owner', 'email_verified_at' => now()]);
        $listing = $this->listingFor($owner);

        $this->actingAs($stranger)->get(route('account.listings.edit', $listing))->assertForbidden();
    }

    public function test_admin_can_approve_a_pending_listing_and_event_is_recorded(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $listing = $this->listingFor($owner);

        $this->actingAs($admin)->patch(route('admin.moderation.update', $listing), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertDatabaseHas('listings', ['id' => $listing->id, 'moderation_status' => 'approved']);
        $this->assertDatabaseHas('moderation_events', ['listing_id' => $listing->id, 'actor_id' => $admin->id, 'action' => 'approved']);
    }

    public function test_non_admin_cannot_access_moderation(): void
    {
        $user = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);

        $this->actingAs($user)->get(route('admin.moderation'))->assertForbidden();
    }

    public function test_owner_can_mark_listing_as_rented_and_reactivate_it(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
        $listing = $this->listingFor($owner);

        $this->actingAs($owner)->patch(route('account.listings.availability', $listing), ['status' => 'rented'])
            ->assertRedirect();
        $this->assertDatabaseHas('listings', ['id' => $listing->id, 'availability_status' => 'rented']);

        $this->actingAs($owner)->patch(route('account.listings.availability', $listing), ['status' => 'available'])
            ->assertRedirect();
        $this->assertDatabaseHas('listings', ['id' => $listing->id, 'availability_status' => 'available']);
        $this->assertNotNull($listing->fresh()->confirmed_at);
    }

    public function test_owner_cannot_reactivate_a_blocked_listing(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
        $listing = $this->listingFor($owner);
        $listing->update(['moderation_status' => 'blocked', 'availability_status' => 'withdrawn']);

        $this->actingAs($owner)->patch(route('account.listings.availability', $listing), ['status' => 'available'])
            ->assertConflict();

        $this->assertSame('withdrawn', $listing->fresh()->availability_status->value);
    }

    private function listingFor(User $owner): Listing
    {
        $district = District::firstOrCreate(['name_uz' => 'Andijon shahri'], ['active' => true]);

        return Listing::create([
            'owner_user_id' => $owner->id,
            'district_id' => $district->id,
            'deal_type' => 'rent',
            'rental_unit' => 'bed',
            'property_type' => 'apartment',
            'students_allowed' => 'yes',
            'title' => 'Sinov uchun e’lon',
            'description' => 'Sinov uchun yetarlicha uzun bo‘lgan e’lon tavsifi.',
            'currency' => 'UZS',
            'price' => 700000,
            'price_basis' => 'monthly_unit',
            'moderation_status' => 'pending',
            'availability_status' => 'available',
        ]);
    }
}
