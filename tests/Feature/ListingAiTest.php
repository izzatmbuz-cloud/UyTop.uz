<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ListingAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_extract_listing_draft(): void
    {
        config(['services.openai.key' => 'test-key', 'services.openai.model' => 'test-model']);
        Http::fake(['api.openai.com/*' => Http::response([
            'output' => [['content' => [['type' => 'output_text', 'text' => json_encode([
                'deal_type' => 'rent', 'rental_unit' => 'bed', 'property_type' => 'apartment',
                'students_allowed' => 'yes', 'title' => 'Talabalar uchun joy', 'description' => null,
                'district_name' => 'Andijon', 'location_text' => 'Universitet yonida', 'currency' => 'UZS',
                'price' => 700000, 'price_basis' => null, 'utilities_mode' => 'unknown', 'utilities_amount' => null,
                'deposit_mode' => null, 'deposit_amount' => null, 'commission_mode' => null, 'commission_amount' => null,
                'capacity' => null, 'free_places' => 2, 'rooms' => 3, 'area_m2' => null, 'amenities' => ['wifi'],
                'review_fields' => ['To‘lov davrini aniqlang'],
            ], JSON_UNESCAPED_UNICODE)]]]],
        ])]);
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->postJson(route('account.listings.ai-parse'), [
            'text' => 'Andijon shahar universitet yonida 3 xonali kvartiraga 2 ta talaba qiz olamiz.',
        ])->assertOk()->assertJsonPath('price', 700000)->assertJsonPath('review_fields.0', 'To‘lov davrini aniqlang');

        Http::assertSent(fn ($request) => $request['store'] === false && $request['text']['format']['strict'] === true);
    }

    public function test_ai_parser_requires_valid_text(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->postJson(route('account.listings.ai-parse'), ['text' => 'short'])
            ->assertUnprocessable()->assertJsonValidationErrors('text');
    }
}
