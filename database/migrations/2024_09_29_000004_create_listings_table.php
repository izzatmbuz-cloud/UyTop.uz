<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');

            // Deal type
            $table->string('deal_type'); // rent / sale
            $table->string('rental_unit')->nullable(); // whole / room / bed
            $table->string('property_type'); // apartment / house / dormitory
            $table->string('dormitory_type')->nullable(); // private / university

            // Students
            $table->string('students_allowed')->nullable(); // yes / no / unknown

            // Title and description
            $table->string('title');
            $table->text('description');

            // Price
            $table->string('currency'); // UZS / USD
            $table->decimal('price', 15, 2)->nullable();
            $table->string('price_basis')->nullable(); // monthly_unit / total / from_total / per_m2 / on_request

            // Utilities
            $table->string('utilities_mode')->nullable(); // included / fixed / unknown
            $table->decimal('utilities_amount', 15, 2)->nullable();
            $table->string('utilities_payment_timing')->nullable(); // move_in / later / unknown

            // Deposit
            $table->string('deposit_mode')->nullable(); // none / fixed / unknown
            $table->decimal('deposit_amount', 15, 2)->nullable();

            // Commission
            $table->string('commission_mode')->nullable(); // none / fixed / unknown
            $table->decimal('commission_amount', 15, 2)->nullable();

            // Capacity
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('free_places')->nullable();

            // Property details
            $table->date('available_from')->nullable();
            $table->unsignedSmallInteger('min_months')->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->unsignedSmallInteger('rooms')->nullable();
            $table->unsignedSmallInteger('floor')->nullable();

            // Location
            $table->text('location_text')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();

            // Source
            $table->string('author_type')->nullable(); // owner / agent / developer
            $table->string('source_type')->nullable(); // direct / telegram / developer / demo
            $table->string('source_url')->nullable();

            // Status
            $table->string('moderation_status')->default('draft');
            $table->string('availability_status')->default('available');

            // Timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            // Demo
            $table->boolean('is_demo')->default(false);

            $table->timestamps();

            // Indexes
            $table->index('owner_user_id');
            $table->index('district_id');
            $table->index(['moderation_status', 'availability_status', 'confirmed_at'], 'listings_status_index');
            $table->index(['deal_type', 'rental_unit'], 'listings_type_index');
            $table->index('is_demo', 'listings_demo_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
