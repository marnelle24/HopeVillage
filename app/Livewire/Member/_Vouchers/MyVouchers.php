<?php

namespace App\Livewire\Member\Vouchers;

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

    public function render()
    {
        return view('livewire.member.vouchers.my-vouchers');
    }
}


