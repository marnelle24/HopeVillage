<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('activity_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('amenity_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('activity_time');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Store additional activity data
            $table->timestamps();
            
            $table->index(['user_id', 'activity_time']);
            $table->index(['location_id', 'activity_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_activities');
    }
};
