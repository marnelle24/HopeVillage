<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->unsignedInteger('per_item_quantity')->default(1)->after('points_cost');
            $table->foreignId('marketplace_category_id')->nullable()->after('name')->constrained('marketplace_categories')->nullOnDelete();
        });

        Schema::create('marketplace_item_location', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['marketplace_item_id', 'location_id'], 'marketplace_item_location_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_item_location');

        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->dropForeign(['marketplace_category_id']);
            $table->dropColumn(['per_item_quantity', 'marketplace_category_id']);
        });

        Schema::dropIfExists('marketplace_categories');
    }
};
