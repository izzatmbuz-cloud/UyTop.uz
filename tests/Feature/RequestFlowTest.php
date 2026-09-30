<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Listing;
use App\Models\Request as RequestModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RequestFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_is_saved_with_an_initial_event(): void
    {
        [$owner, $listing] = $this->listing();
        $requester = User::factory()->create();

        $this->actingAs($requester)->post("/listings/{$listing->id}/requests", [
            'name' => 'Talaba', 'phone' => '+998 90 123 45 67', 'occupants_count' => 1,
            'idempotency_key' => (string) Str::uuid(),
        ])->assertRedirect('/account/requests');

        $created = RequestModel::first();
        $this->assertSame($owner->id, $created->recipient_id);
        $this->assertDatabaseHas('request_events', ['request_id' => $created->id, 'from_status' => 'created', 'to_status' => 'new']);
    }

    public function test_owner_cannot_request_their_own_listing(): void
    {
        [$owner, $listing] = $this->listing();

        $this->actingAs($owner)->post("/listings/{$listing->id}/requests", [
            'name' => 'Owner', 'phone' => '+998 90 123 45 67', 'occupants_count' => 1,
            'idempotency_key' => (string) Str::uuid(),
        ])->assertForbidden();
    }

    private function listing(): array
    {
        $owner = User::factory()->create();
        $district = District::create(['name_uz' => 'Andijon', 'active' => true]);
        $listing = Listing::create([
            'owner_user_id' => $owner->id, 'district_id' => $district->id,
            'deal_type' => 'rent', 'rental_unit' => 'bed', 'property_type' => 'apartment',
            'students_allowed' => 'yes', 'title' => 'Demo', 'description' => 'Demo',
            'currency' => 'UZS', 'price' => 700000, 'price_basis' => 'monthly_unit',
            'utilities_mode' => 'included', 'deposit_mode' => 'none', 'commission_mode' => 'none',
            'free_places' => 2, 'moderation_status' => 'approved', 'availability_status' => 'available', 'confirmed_at' => now(),
        ]);

        return [$owner, $listing];
    }
}
