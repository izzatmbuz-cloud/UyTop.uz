<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->unique(['listing_id', 'reporter_id'], 'reports_listing_reporter_unique');
            $table->index(['status', 'created_at'], 'reports_status_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropUnique('reports_listing_reporter_unique');
            $table->dropIndex('reports_status_created_index');
        });
    }
};
