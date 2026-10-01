<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\District;
use App\Models\Listing;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::updateOrCreate(['email' => 'owner@uytop.uz'], ['name' => 'Demo uy egasi', 'phone' => '+998 90 111 22 33', 'role' => 'owner', 'email_verified_at' => now(), 'password' => Hash::make('password')]);
        User::updateOrCreate(['email' => 'student@uytop.uz'], ['name' => 'Demo talaba', 'phone' => '+998 90 444 55 66', 'role' => 'user', 'email_verified_at' => now(), 'password' => Hash::make('password')]);
        User::updateOrCreate(['email' => 'admin@uytop.uz'], ['name' => 'Administrator', 'phone' => '+998 90 777 88 99', 'role' => 'admin', 'email_verified_at' => now(), 'password' => Hash::make('password')]);

        $districts = collect(['Andijon shahri', 'Asaka', 'Baliqchi', 'Shahrixon', 'Paxtaobod', 'Marhamat'])
            ->map(fn (string $name) => District::updateOrCreate(['name_uz' => $name], ['active' => true]));

        $amenities = collect([
            ['code' => 'wifi', 'name_uz' => 'Wi-Fi'], ['code' => 'furniture', 'name_uz' => 'Mebel'],
            ['code' => 'washing_machine', 'name_uz' => 'Kir yuvish mashinasi'], ['code' => 'air_conditioner', 'name_uz' => 'Konditsioner'],
        ])->map(fn (array $data) => Amenity::updateOrCreate(['code' => $data['code']], $data))->values();

        $project = Project::updateOrCreate(['name' => 'Navbahor Residence'], [
            'manager_user_id' => $owner->id, 'district_id' => $districts[0]->id,
            'name' => 'Navbahor Residence', 'developer_name' => 'UyTop Demo Development',
            'description' => 'Shahar markaziga yaqin, energiya tejamkor yangi turar joy majmuasi.',
            'stage' => 'building', 'completion_text' => '2027-yil III chorak',
            'moderation_status' => 'approved', 'is_demo' => true,
        ]);

        $titles = [
            'Universitet yaqinida talabalar uchun o‘rin', 'Markazda yorug‘ xona', 'Ikki talaba uchun qulay xona',
            'Bozor yaqinida alohida o‘rin', 'Oilaga va talabalarga mos kvartira', 'Tinch hududdagi ikki xonali uy',
            'Wi-Fi bilan jihozlangan xona', 'Bekat yaqinidagi talabalar uyi', 'Qizlar uchun ozoda o‘rin',
            'Yigitlar uchun arzon xona', 'Yangi ta’mirlangan kvartira', 'Hovlili uy ijaraga beriladi',
            'Markaziy ko‘chadagi bir xonali uy', 'Universitetgacha piyoda 10 daqiqa', 'Uch kishilik keng xona',
            'Kommunal to‘lovlari kiritilgan o‘rin', 'Depozitsiz talabalar xonasi', 'Uzoq muddatga butun kvartira',
            'Asaka markazida sotiladigan kvartira', 'Yangi qurilishdagi ikki xonali kvartira',
            'Hovlili uy sotiladi', 'Tayyor ta’mirli yangi kvartira', 'Shahrixonda uch xonali uy', 'Markazda ofisga mos kvartira',
        ];

        foreach ($titles as $index => $title) {
            $sale = $index >= 18;
            $unit = $sale ? null : (['bed', 'room', 'whole'][$index % 3]);
            $currency = $index % 8 === 0 ? 'USD' : 'UZS';
            $price = $sale
                ? ($currency === 'USD' ? 42000 + ($index * 900) : 260000000 + ($index * 3500000))
                : ($currency === 'USD' ? 90 + ($index * 4) : 550000 + ($index * 85000));
            $utilitiesMode = $index % 4 === 0 ? 'unknown' : ($index % 3 === 0 ? 'included' : 'fixed');

            $listing = Listing::updateOrCreate(['title' => $title, 'source_type' => 'demo'], [
                'owner_user_id' => $owner->id,
                'project_id' => in_array($index, [19, 21], true) ? $project->id : null,
                'district_id' => $districts[$index % $districts->count()]->id,
                'deal_type' => $sale ? 'sale' : 'rent',
                'rental_unit' => $unit,
                'property_type' => $index % 7 === 0 ? 'house' : 'apartment',
                'students_allowed' => $sale ? null : ($index % 5 === 0 ? 'unknown' : 'yes'),
                'title' => $title,
                'description' => 'Demo e’lon. Shartlar, xarajatlar va mavjudlik foydalanuvchi tomonidan nashrdan oldin tekshiriladi.',
                'currency' => $currency,
                'price' => $price,
                'price_basis' => $sale ? ($index % 3 === 0 ? 'from_total' : 'total') : 'monthly_unit',
                'utilities_mode' => $sale ? 'unknown' : $utilitiesMode,
                'utilities_amount' => $utilitiesMode === 'fixed' ? ($currency === 'USD' ? 15 : 120000) : null,
                'utilities_payment_timing' => $utilitiesMode === 'fixed' ? ($index % 2 ? 'later' : 'move_in') : 'unknown',
                'deposit_mode' => $sale ? 'none' : ($index % 4 === 1 ? 'unknown' : 'fixed'),
                'deposit_amount' => ! $sale && $index % 4 !== 1 ? $price : null,
                'commission_mode' => $index % 6 === 0 ? 'unknown' : 'none',
                'commission_amount' => null,
                'capacity' => $sale ? null : 2 + ($index % 4),
                'free_places' => $sale ? null : 1 + ($index % 3),
                'available_from' => $sale ? null : now()->addDays($index % 10)->toDateString(),
                'min_months' => $sale ? null : 3 + ($index % 4),
                'area_m2' => 24 + ($index * 3),
                'rooms' => 1 + ($index % 4),
                'floor' => 1 + ($index % 9),
                'location_text' => $districts[$index % $districts->count()]->name_uz.' markazi',
                'author_type' => $sale && $index % 2 ? 'developer' : 'owner',
                'source_type' => 'demo',
                'moderation_status' => 'approved', 'availability_status' => 'available',
                'confirmed_at' => now()->subDays($index % 7), 'published_at' => now()->subDays($index), 'is_demo' => true,
            ]);

            $listing->amenities()->sync([$amenities[0]->id, $amenities[1 + ($index % 3)]->id]);
        }
    }
}
