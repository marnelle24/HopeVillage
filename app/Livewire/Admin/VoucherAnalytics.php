<?php

namespace App\Livewire\Admin;

use App\Models\Voucher;
use Livewire\Component;

class VoucherAnalytics extends Component
{
    public function getVoucherStatsProperty()
    {
        $totalVouchers = Voucher::count();
        $activeVouchers = Voucher::where('is_active', true)->count();
        $totalRedeemed = Voucher::sum('usage_count');
        $totalIssued = Voucher::sum('usage_limit') ?: 1;
        $redemptionRate = $totalIssued > 0 ? round(($totalRedeemed / $totalIssued) * 100, 1) : 0;

        return [
            'total' => $totalVouchers,
            'active' => $activeVouchers,
            'redeemed' => $totalRedeemed,
            'redemptionRate' => $redemptionRate,
        ];
    }

    public function render()
    {
        return view('livewire.admin.voucher-analytics', [
            'voucherStats' => $this->voucherStats,
        ]);
    }
}
