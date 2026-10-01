<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ListingTextParser
{
    public function parse(string $text): array
    {
        $key = config('services.openai.key');
        throw_unless($key, RuntimeException::class, 'OPENAI_API_KEY sozlanmagan.');

        $response = Http::withToken($key)->timeout(35)->retry(2, 300)
            ->post('https://api.openai.com/v1/responses', [
                'model' => config('services.openai.model'),
                'store' => false,
                'instructions' => $this->instructions(),
                'input' => $text,
                'text' => ['format' => ['type' => 'json_schema', 'name' => 'listing_draft', 'strict' => true, 'schema' => $this->schema()]],
            ]);

        if ($response->failed()) {
            report(new RuntimeException('OpenAI API error: '.$response->status()));
            abort(503, 'AI xizmati vaqtincha ishlamayapti. Formani qo‘lda to‘ldiring.');
        }

        $output = collect($response->json('output', []))->flatMap(fn ($item) => $item['content'] ?? [])->firstWhere('type', 'output_text');
        $data = json_decode($output['text'] ?? '', true);
        if (! is_array($data)) {
            abort(503, 'AI javobini o‘qib bo‘lmadi. Formani qo‘lda to‘ldiring.');
        }

        return $data;
    }

    private function instructions(): string
    {
        return <<<'PROMPT'
You extract a housing listing draft from Uzbek or Russian Telegram text. Never invent factual details. Use null for every fact not stated. You may create a short neutral title and clean description only from stated facts. Return the mentioned city or district in district_name and landmarks such as "universitet yonida" in location_text. Price is one numeric amount without separators. "Odam boshiga", "joy", "o‘rin", or accepting N people into an existing apartment means rental_unit=bed; "N ta olamiz/qabul qilamiz" means free_places=N. Use whole only when the whole home is offered, and room only when a separate room is offered. Map deal_type to rent/sale; property_type to apartment/house/dormitory; students_allowed to yes/no/unknown; currency to UZS/USD; price_basis to monthly_unit/total/from_total/per_m2/on_request. Return explicitly mentioned wifi or furniture codes in amenities. Utilities, deposit and commission modes must use only schema values. Put short Uzbek explanations of missing or ambiguous important facts in review_fields. Do not copy phone numbers into description or location.

Example: "3 xonali kvartiraga 2 ta talaba qiz olamiz. Odam boshiga 700 ming, kommunal alohida. Wi-Fi bor" means rental_unit=bed, students_allowed=yes, free_places=2, rooms=3, price=700000, currency=UZS, price_basis=monthly_unit, utilities_mode=unknown, amenities=["wifi"]. Do not treat "kommunal alohida" as a known fixed amount.
PROMPT;
    }

    private function schema(): array
    {
        $nullableString = fn (?array $enum = null) => ['anyOf' => [['type' => 'string', ...($enum ? ['enum' => $enum] : [])], ['type' => 'null']]];
        $nullableNumber = ['anyOf' => [['type' => 'number'], ['type' => 'null']]];
        $properties = [
            'deal_type' => $nullableString(['rent', 'sale']), 'rental_unit' => $nullableString(['whole', 'room', 'bed']),
            'property_type' => $nullableString(['apartment', 'house', 'dormitory']), 'students_allowed' => $nullableString(['yes', 'no', 'unknown']),
            'title' => $nullableString(), 'description' => $nullableString(), 'district_name' => $nullableString(), 'location_text' => $nullableString(),
            'currency' => $nullableString(['UZS', 'USD']), 'price' => $nullableNumber,
            'price_basis' => $nullableString(['monthly_unit', 'total', 'from_total', 'per_m2', 'on_request']),
            'utilities_mode' => $nullableString(['included', 'fixed', 'unknown']), 'utilities_amount' => $nullableNumber,
            'deposit_mode' => $nullableString(['none', 'fixed', 'unknown']), 'deposit_amount' => $nullableNumber,
            'commission_mode' => $nullableString(['none', 'fixed', 'unknown']), 'commission_amount' => $nullableNumber,
            'capacity' => $nullableNumber, 'free_places' => $nullableNumber, 'rooms' => $nullableNumber, 'area_m2' => $nullableNumber,
            'amenities' => ['type' => 'array', 'items' => ['type' => 'string', 'enum' => ['wifi', 'furniture']]],
            'review_fields' => ['type' => 'array', 'items' => ['type' => 'string']],
        ];

        return ['type' => 'object', 'properties' => $properties, 'required' => array_keys($properties), 'additionalProperties' => false];
    }
}
