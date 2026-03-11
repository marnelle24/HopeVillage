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
        Schema::create('admin_voucher_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_voucher_ledger_entry_id')
                ->constrained()
                ->name('av_reimb_ledger_entry_id_fk')
                ->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('reimbursed_at');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->name('av_reimb_created_by_fk')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_voucher_reimbursements');
    }
};
