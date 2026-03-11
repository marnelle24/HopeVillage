<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminVoucherLedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'admin_voucher_id',
        'period_month',
        'total_redemptions',
        'total_amount_dispensed',
    ];

    protected function casts(): array
    {
        return [
            'period_month' => 'date',
            'total_redemptions' => 'integer',
            'total_amount_dispensed' => 'decimal:2',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function adminVoucher(): BelongsTo
    {
        return $this->belongsTo(AdminVoucher::class, 'admin_voucher_id');
    }

    public function reimbursements(): HasMany
    {
        return $this->hasMany(AdminVoucherReimbursement::class, 'admin_voucher_ledger_entry_id');
    }

    /**
     * Get the total amount reimbursed for this ledger entry.
     */
    public function getTotalReimbursedAttribute(): float
    {
        return (float) $this->reimbursements()->sum('amount');
    }

    /**
     * Get the outstanding balance (total dispensed minus total reimbursed).
     */
    public function getOutstandingBalanceAttribute(): float
    {
        return (float) $this->total_amount_dispensed - $this->total_reimbursed;
    }
}
