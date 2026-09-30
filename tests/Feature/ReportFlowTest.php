<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_report_a_public_listing_only_once(): void
    {
        $listing = $this->publicListing();
        $reporter = User::factory()->create();

        $this->actingAs($reporter)->post(route('reports.store', $listing), [
            'reason' => 'incorrect',
            'comment' => 'Narx ma’lumoti noto‘g‘ri ko‘rsatilgan.',
        ])->assertRedirect();

        $this->actingAs($reporter)->post(route('reports.store', $listing), [
            'reason' => 'duplicate',
        ])->assertRedirect();

        $this->assertDatabaseCount('reports', 1);
    }

    public function test_admin_can_resolve_report_and_block_listing(): void
    {
        $listing = $this->publicListing();
        $reporter = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $report = $listing->reports()->create(['reporter_id' => $reporter->id, 'reason' => 'fraud', 'status' => 'new']);

        $this->actingAs($admin)->patch(route('admin.reports.update', $report), [
            'status' => 'resolved',
            'resolution' => 'Ma’lumot tasdiqlanmadi, e’lon bloklandi.',
            'block_listing' => true,
        ])->assertRedirect();

        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'resolved', 'resolver_id' => $admin->id]);
        $this->assertSame('blocked', $listing->fresh()->moderation_status->value);
        $this->assertDatabaseHas('moderation_events', ['listing_id' => $listing->id, 'action' => 'blocked']);
    }

    public function test_non_admin_cannot_resolve_report(): void
    {
        $listing = $this->publicListing();
        $reporter = User::factory()->create();
        $report = $listing->reports()->create(['reporter_id' => $reporter->id, 'reason' => 'other', 'status' => 'new']);

        $this->actingAs($reporter)->patch(route('admin.reports.update', $report), [
            'status' => 'dismissed',
            'resolution' => 'No access.',
        ])->assertForbidden();
    }

    private function publicListing(): Listing
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $district = District::create(['name_uz' => 'Andijon shahri', 'active' => true]);

        return Listing::create([
            'owner_user_id' => $owner->id, 'district_id' => $district->id,
            'deal_type' => 'rent', 'rental_unit' => 'bed', 'property_type' => 'apartment',
            'students_allowed' => 'yes', 'title' => 'Ommaviy sinov e’loni',
            'description' => 'Shikoyat oqimini tekshirish uchun yetarli tavsif.',
            'currency' => 'UZS', 'price' => 700000, 'price_basis' => 'monthly_unit',
            'moderation_status' => 'approved', 'availability_status' => 'available', 'confirmed_at' => now(),
        ]);
    }
}
