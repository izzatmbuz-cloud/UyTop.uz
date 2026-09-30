<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_amenity', function (Blueprint $table) {
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained('amenities')->onDelete('cascade');

            $table->primary(['listing_id', 'amenity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_amenity');
    }
};
