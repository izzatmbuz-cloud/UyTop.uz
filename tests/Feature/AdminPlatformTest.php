<?php

namespace Tests\Feature;

use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPlatformTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_commission_and_freshness_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($admin)->patch(route('admin.settings.update'), [
            'rent_commission_percent' => 15,
            'sale_commission_percent' => 3,
            'listing_confirmation_days' => 7,
        ])->assertRedirect();

        $this->assertSame(15.0, PlatformSetting::number('rent_commission_percent', 20));
        $this->assertSame(3.0, PlatformSetting::number('sale_commission_percent', 5));
    }

    public function test_regular_user_cannot_update_platform_settings(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->patch(route('admin.settings.update'), [
            'rent_commission_percent' => 15,
            'sale_commission_percent' => 3,
            'listing_confirmation_days' => 7,
        ])->assertForbidden();
    }
}
