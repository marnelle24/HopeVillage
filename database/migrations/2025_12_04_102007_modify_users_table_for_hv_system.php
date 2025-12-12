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
            $table->string('whatsapp_number')->nullable()->after('email');
            $table->enum('user_type', ['admin', 'member', 'merchant_user'])->default('member')->after('password');
            $table->string('qr_code')->nullable()->unique()->after('user_type');
            $table->boolean('is_verified')->default(false)->after('qr_code');
            $table->integer('total_points')->default(0)->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'user_type', 'qr_code', 'is_verified', 'total_points']);
        });
    }
};
