<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminVoucherReimbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_voucher_ledger_entry_id',
        'amount',
        'reimbursed_at',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'reimbursed_at' => 'date',
        ];
    }

    public function ledgerEntry(): BelongsTo
    {
        return $this->belongsTo(AdminVoucherLedgerEntry::class, 'admin_voucher_ledger_entry_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
