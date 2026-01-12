<?php

namespace App\Livewire\AdminVouchers;

use App\Models\AdminVoucher;
use Livewire\Component;

class MerchantRedemptionSummary extends Component
{
    public $voucherCode;
    public $voucher;

    public function mount($voucherCode)
    {
        $this->voucherCode = $voucherCode;
        $this->loadVoucher();
    }

    public function loadVoucher()
    {
        $this->voucher = AdminVoucher::where('voucher_code', $this->voucherCode)->first();
    }

    public function getRedemptionByMerchantProperty()
    {
        if (!$this->voucher) {
            return [
                'labels' => [],
                'data' => [],
                'merchants' => [],
            ];
        }

        // Get all redeemed members with their merchant
        $redeemedMembers = $this->voucher->users()
            ->wherePivot('status', 'redeemed')
            ->whereNotNull('user_admin_voucher.redeemed_at_merchant_id')
            ->get();

        // Group by merchant
        $grouped = $redeemedMembers->groupBy(function ($user) {
            return $user->pivot->redeemed_at_merchant_id;
        });

        // Get merchant details and counts
        $merchantData = [];
        foreach ($grouped as $merchantId => $members) {
            $merchant = \App\Models\Merchant::find($merchantId);
            if ($merchant) {
                $merchantData[] = [
                    'id' => $merchant->id,
                    'name' => $merchant->name,
                    'count' => $members->count(),
                ];
            }
        }

        // Sort by count descending
        usort($merchantData, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        // Prepare chart data - truncate long merchant names for chart
        $labels = array_map(function ($merchant) {
            $name = $merchant['name'];
            $words = explode(' ', $name);
            if (count($words) > 3) {
                return implode(' ', array_slice($words, 0, 3)) . '...';
            }
            return $name;
        }, $merchantData);
        
        $data = array_column($merchantData, 'count');

        return [
            'labels' => $labels,
            'data' => $data,
            'merchants' => $merchantData,
        ];
    }

    public function render()
    {
        return view('livewire.admin-vouchers.merchant-redemption-summary', [
            'redemptionData' => $this->redemptionByMerchant,
        ]);
    }
}
