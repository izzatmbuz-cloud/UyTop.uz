<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Listing;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $districts = [
            ['name_uz' => 'Bo‘ston', 'active' => true],
            ['name_uz' => 'Markaziy', 'active' => true],
            ['name_uz' => 'Asaka', 'active' => true],
            ['name_uz' => 'Paxtaobod', 'active' => true],
        ];

        foreach ($districts as $district) {
            District::create($district);
        }

        $districtIds = District::pluck('id')->all();

        $project = Project::create([
            'manager_user_id' => $user->id,
            'district_id' => $districtIds[0],
            'name' => 'Samarqand ko‘chasi loyihasi',
            'developer_name' => 'Demo Developer',
            'description' => 'Yangi loyiha demo ma’lumot uchun yaratilgan.',
            'stage' => 'building',
            'completion_text' => '2027 yil',
            'moderation_status' => 'approved',
            'is_demo' => true,
        ]);

        $listingData = [
            [
                'owner_user_id' => $user->id,
                'project_id' => $project->id,
                'district_id' => $districtIds[0],
                'deal_type' => 'rent',
                'rental_unit' => 'whole',
                'property_type' => 'apartment',
                'students_allowed' => 'yes',
                'title' => '2 xonali kvartira, qulay narxda',
                'description' => 'Yaqin joylashgan, toza va qulay kvartira. Talabalar uchun mos.',
                'currency' => 'UZS',
                'price' => 9000000,
                'price_basis' => 'monthly_unit',
                'utilities_mode' => 'fixed',
                'utilities_amount' => 180000,
                'utilities_payment_timing' => 'later',
                'deposit_mode' => 'fixed',
                'deposit_amount' => 5000000,
                'commission_mode' => 'none',
                'commission_amount' => 0,
                'capacity' => 4,
                'free_places' => 1,
                'available_from' => now()->addDays(7)->toDateString(),
                'min_months' => 6,
                'area_m2' => 58,
                'rooms' => 2,
                'floor' => 5,
                'location_text' => 'Bo‘ston ko‘chasi',
                'moderation_status' => 'approved',
                'availability_status' => 'available',
                'confirmed_at' => now(),
                'published_at' => now(),
                'is_demo' => true,
            ],
            [
                'owner_user_id' => $user->id,
                'project_id' => null,
                'district_id' => $districtIds[1],
                'deal_type' => 'rent',
                'rental_unit' => 'room',
                'property_type' => 'apartment',
                'students_allowed' => 'yes',
                'title' => 'Yotoqxona xonasi, talabalar uchun',
                'description' => 'O‘qishga yaqin, qulay joylashgan xona. Kommunal to‘lovlar kiritilgan.',
                'currency' => 'UZS',
                'price' => 2600000,
                'price_basis' => 'monthly_unit',
                'utilities_mode' => 'included',
                'utilities_amount' => 0,
                'utilities_payment_timing' => 'move_in',
                'deposit_mode' => 'fixed',
                'deposit_amount' => 1500000,
                'commission_mode' => 'none',
                'commission_amount' => 0,
                'capacity' => 2,
                'free_places' => 1,
                'available_from' => now()->toDateString(),
                'min_months' => 3,
                'area_m2' => 18,
                'rooms' => 1,
                'floor' => 2,
                'location_text' => 'Markaziy tumani',
                'moderation_status' => 'approved',
                'availability_status' => 'available',
                'confirmed_at' => now(),
                'published_at' => now(),
                'is_demo' => true,
            ],
            [
                'owner_user_id' => $user->id,
                'project_id' => null,
                'district_id' => $districtIds[2],
                'deal_type' => 'sale',
                'rental_unit' => null,
                'property_type' => 'apartment',
                'students_allowed' => null,
                'title' => 'Sotuvdagi kvartira',
                'description' => 'Yashash uchun qulay, kerakli barcha shartlar mavjud.',
                'currency' => 'UZS',
                'price' => 280000000,
                'price_basis' => 'total',
                'utilities_mode' => 'unknown',
                'utilities_amount' => null,
                'utilities_payment_timing' => 'unknown',
                'deposit_mode' => 'none',
                'deposit_amount' => 0,
                'commission_mode' => 'unknown',
                'commission_amount' => null,
                'capacity' => 5,
                'free_places' => null,
                'available_from' => now()->toDateString(),
                'min_months' => null,
                'area_m2' => 70,
                'rooms' => 3,
                'floor' => 8,
                'location_text' => 'Asaka ko‘chasi',
                'moderation_status' => 'approved',
                'availability_status' => 'available',
                'confirmed_at' => now(),
                'published_at' => now(),
                'is_demo' => true,
            ],
        ];

        foreach ($listingData as $data) {
            Listing::create($data);
        }
    }
}
