<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->unique()->constrained('requests')->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignId('payer_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('deal_type');
            $table->decimal('rate_percent', 5, 2);
            $table->decimal('deal_amount', 15, 2)->nullable();
            $table->decimal('commission_amount', 15, 2)->nullable();
            $table->string('currency', 3);
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        DB::table('platform_settings')->insert([
            ['key' => 'rent_commission_percent', 'value' => '20', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'sale_commission_percent', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'listing_confirmation_days', 'value' => '7', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('platform_settings');
    }
};
