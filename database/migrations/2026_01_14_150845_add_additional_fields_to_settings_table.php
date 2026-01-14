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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->text('description')->nullable()->after('name');
            $table->foreignId('added_by')->nullable()->after('description')->constrained('users')->onDelete('set null');
            $table->boolean('status')->default(true)->after('added_by');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropColumn(['name', 'description', 'added_by', 'status', 'deleted_at']);
        });
    }
};
