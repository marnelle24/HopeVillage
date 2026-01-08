<?php

namespace App\Livewire\Member\VouchersV2;

use Illuminate\Support\Collection;
use Livewire\Component;

class MyVouchers extends Component
{
    public function getClaimedProperty(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        return $user->vouchers()
            ->with('merchant')
            ->wherePivot('status', 'claimed')
            ->latest('user_voucher.claimed_at')
            ->get();
    }

    public function getRedeemedProperty(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        return $user->vouchers()
            ->with('merchant')
            ->wherePivot('status', 'redeemed')
            ->latest('user_voucher.redeemed_at')
            ->get();
    }

    public function getClaimedAdminVouchersProperty(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        return $user->adminVouchers()
            ->with('merchants')
            ->wherePivot('status', 'claimed')
            ->latest('user_admin_voucher.claimed_at')
            ->get();
    }

    public function getRedeemedAdminVouchersProperty(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        return $user->adminVouchers()
            ->with('merchants')
            ->wherePivot('status', 'redeemed')
            ->latest('user_admin_voucher.redeemed_at')
            ->get();
    }

    public function getAllClaimedVouchersProperty(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        $claimedMerchant = $this->claimed->map(function ($voucher) {
            return (object) [
                'id' => $voucher->id,
                'voucher_code' => $voucher->voucher_code,
                'name' => $voucher->name,
                'type' => 'merchant',
                'merchant' => $voucher->merchant,
                'claimed_at' => $voucher->pivot->claimed_at,
            ];
        });

        $claimedAdmin = $this->claimedAdminVouchers->map(function ($adminVoucher) {
            return (object) [
                'id' => $adminVoucher->id,
                'voucher_code' => $adminVoucher->voucher_code,
                'name' => $adminVoucher->name,
                'type' => 'admin',
                'merchants' => $adminVoucher->merchants,
                'points_cost' => $adminVoucher->points_cost,
                'claimed_at' => $adminVoucher->pivot->claimed_at,
            ];
        });

        // Combine and sort by claimed_at (most recent first)
        return $claimedMerchant->concat($claimedAdmin)
            ->sortByDesc(function ($voucher) {
                return $voucher->claimed_at ? \Carbon\Carbon::parse($voucher->claimed_at)->timestamp : 0;
            })
            ->values();
    }

    public function getAllRedeemedVouchersProperty(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        $redeemedMerchant = $this->redeemed->map(function ($voucher) {
            return (object) [
                'id' => $voucher->id,
                'voucher_code' => $voucher->voucher_code,
                'name' => $voucher->name,
                'description' => $voucher->description,
                'image_url' => $voucher->image_url,
                'type' => 'merchant',
                'merchant' => $voucher->merchant,
                'redeemed_at' => $voucher->pivot->redeemed_at,
            ];
        });

        $redeemedAdmin = $this->redeemedAdminVouchers->map(function ($adminVoucher) {
            // Get the merchant where it was redeemed
            $redeemedMerchant = null;
            if ($adminVoucher->pivot->redeemed_at_merchant_id) {
                $redeemedMerchant = \App\Models\Merchant::find($adminVoucher->pivot->redeemed_at_merchant_id);
            }

            return (object) [
                'id' => $adminVoucher->id,
                'voucher_code' => $adminVoucher->voucher_code,
                'name' => $adminVoucher->name,
                'description' => $adminVoucher->description,
                'image_url' => $adminVoucher->image_url,
                'type' => 'admin',
                'merchants' => $adminVoucher->merchants,
                'redeemed_merchant' => $redeemedMerchant, // Merchant where it was redeemed
                'points_cost' => $adminVoucher->points_cost,
                'redeemed_at' => $adminVoucher->pivot->redeemed_at,
            ];
        });

        // Combine and sort by redeemed_at (most recent first)
        return $redeemedMerchant->concat($redeemedAdmin)
            ->sortByDesc(function ($voucher) {
                return $voucher->redeemed_at ? \Carbon\Carbon::parse($voucher->redeemed_at)->timestamp : 0;
            })
            ->values();
    }

    public function render()
    {
        return view('livewire.member.vouchers-v2.my-vouchers');
    }
}

