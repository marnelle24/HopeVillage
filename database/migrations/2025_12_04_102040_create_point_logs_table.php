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
        Schema::create('point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_activity_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('point_system_config_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('activity_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('amenity_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('points');
            $table->text('description')->nullable();
            $table->timestamp('awarded_at');
            $table->timestamps();
            
            $table->index(['user_id', 'awarded_at']);
            $table->index(['location_id', 'awarded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_logs');
    }
};
