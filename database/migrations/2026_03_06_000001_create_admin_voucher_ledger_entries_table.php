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
        Schema::create('admin_voucher_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_voucher_id')->constrained()->onDelete('cascade');
            $table->date('period_month')->comment('First day of month e.g. 2026-01-01');
            $table->unsignedInteger('total_redemptions')->default(0);
            $table->decimal('total_amount_dispensed', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['merchant_id', 'admin_voucher_id', 'period_month'], 'av_ledger_merchant_voucher_month_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_voucher_ledger_entries');
    }
};
