<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('listing_id')->nullable()->constrained('listings')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            
            $table->string('name');
            $table->string('phone');
            $table->timestamp('proposed_at')->nullable();
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->unsignedSmallInteger('occupants_count')->nullable();
            $table->text('message')->nullable();
            
            $table->string('status')->default('new');
            $table->string('idempotency_key')->unique()->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('recipient_id');
            $table->index(['status', 'created_at']);
            $table->index('requester_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
