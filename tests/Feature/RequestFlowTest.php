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

    public function test_owner_can_propose_another_time_and_requester_can_accept_it(): void
    {
        [$owner, $listing] = $this->listing();
        $requester = User::factory()->create();
        $request = $this->createRequest($requester, $owner, $listing);
        $date = now()->addDays(2)->toDateString();

        $this->actingAs($owner)->patch(route('requests.status', $request), [
            'status' => 'alternative_proposed',
            'proposed_at' => $date,
            'time_start' => '15:00',
            'time_end' => '16:00',
            'comment' => 'Shu vaqtda ko‘rsatishimiz mumkin.',
        ])->assertRedirect();

        $this->actingAs($requester)->patch(route('requests.status', $request), [
            'status' => 'accepted',
        ])->assertRedirect();

        $this->assertSame('accepted', $request->fresh()->status->value);
        $this->assertDatabaseHas('request_events', [
            'request_id' => $request->id,
            'from_status' => 'new',
            'to_status' => 'alternative_proposed',
            'comment' => 'Shu vaqtda ko‘rsatishimiz mumkin.',
        ]);
        $this->assertDatabaseHas('request_events', [
            'request_id' => $request->id,
            'from_status' => 'alternative_proposed',
            'to_status' => 'accepted',
        ]);
    }

    public function test_requester_cannot_complete_a_request(): void
    {
        [$owner, $listing] = $this->listing();
        $requester = User::factory()->create();
        $request = $this->createRequest($requester, $owner, $listing);

        $this->actingAs($requester)->patch(route('requests.status', $request), [
            'status' => 'completed',
        ])->assertForbidden();

        $this->assertSame('new', $request->fresh()->status->value);
    }

    public function test_completed_rental_creates_commission_and_marks_listing_rented(): void
    {
        [$owner, $listing] = $this->listing();
        $requester = User::factory()->create();
        $request = $this->createRequest($requester, $owner, $listing);

        $this->actingAs($owner)->patch(route('requests.status', $request), ['status' => 'accepted'])->assertRedirect();
        $this->actingAs($owner)->patch(route('requests.status', $request), ['status' => 'completed'])->assertRedirect();

        $this->assertSame('rented', $listing->fresh()->availability_status->value);
        $this->assertDatabaseHas('commissions', [
            'request_id' => $request->id,
            'rate_percent' => 20,
            'commission_amount' => 140000,
            'status' => 'pending',
        ]);
    }

    public function test_alternative_requires_a_future_date(): void
    {
        [$owner, $listing] = $this->listing();
        $requester = User::factory()->create();
        $request = $this->createRequest($requester, $owner, $listing);

        $this->actingAs($owner)->patch(route('requests.status', $request), [
            'status' => 'alternative_proposed',
        ])->assertSessionHasErrors('proposed_at');

        $this->assertSame('new', $request->fresh()->status->value);
    }

    private function createRequest(User $requester, User $owner, Listing $listing): RequestModel
    {
        return RequestModel::create([
            'requester_id' => $requester->id,
            'recipient_id' => $owner->id,
            'listing_id' => $listing->id,
            'name' => $requester->name,
            'phone' => '+998 90 123 45 67',
            'status' => 'new',
            'idempotency_key' => (string) Str::uuid(),
        ]);
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
