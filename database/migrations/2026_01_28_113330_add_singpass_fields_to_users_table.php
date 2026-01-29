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
        Schema::table('users', function (Blueprint $table) {
            $table->string('singpass_sub')->nullable()->unique()->after('email');
            $table->string('singpass_uuid')->nullable()->unique()->after('singpass_sub');
            $table->timestamp('singpass_verified_at')->nullable()->after('singpass_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['singpass_sub', 'singpass_uuid', 'singpass_verified_at']);
        });
    }
};
