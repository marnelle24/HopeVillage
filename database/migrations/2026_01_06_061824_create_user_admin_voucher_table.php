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
        Schema::create('user_admin_voucher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_voucher_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['claimed', 'redeemed'])->default('claimed');
            $table->dateTime('claimed_at')->nullable();
            $table->dateTime('redeemed_at')->nullable();
            $table->foreignId('redeemed_at_merchant_id')->nullable()->constrained('merchants')->onDelete('set null');
            $table->timestamps();

            $table->unique(['admin_voucher_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_admin_voucher');
    }
};
