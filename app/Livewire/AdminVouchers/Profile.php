<?php

namespace App\Livewire\AdminVouchers;

use App\Models\AdminVoucher;
use App\Services\QrCodeService;
use Livewire\Component;

class Profile extends Component
{
    public $voucherCode;
    public $voucher;

    public function mount($voucher_code)
    {
        $this->voucherCode = $voucher_code;
        $this->loadVoucher();
    }

    public function loadVoucher()
    {
        $this->voucher = AdminVoucher::with(['merchants', 'createdBy'])
            ->where('voucher_code', $this->voucherCode)
            ->firstOrFail();
    }

    public function getClaimedMembersProperty()
    {
        return $this->voucher->users()
            ->wherePivot('status', 'claimed')
            ->orderBy('user_admin_voucher.claimed_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'fin' => $user->fin,
                    'claimed_at' => $user->pivot->claimed_at,
                ];
            });
    }

    public function getRedeemedMembersProperty()
    {
        return $this->voucher->users()
            ->wherePivot('status', 'redeemed')
            ->orderBy('user_admin_voucher.redeemed_at', 'desc')
            ->get()
            ->map(function ($user) {
                $merchant = null;
                if ($user->pivot->redeemed_at_merchant_id) {
                    $merchant = \App\Models\Merchant::find($user->pivot->redeemed_at_merchant_id);
                }
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'fin' => $user->fin,
                    'claimed_at' => $user->pivot->claimed_at,
                    'redeemed_at' => $user->pivot->redeemed_at,
                    'merchant' => $merchant,
                ];
            });
    }

    public function render()
    {
        $qrCodeService = app(QrCodeService::class);
        $qrCodeImage = $qrCodeService->generateQrCodeImage($this->voucher->voucher_code, 400);

        return view('livewire.admin-vouchers.profile', [
            'voucher' => $this->voucher,
            'claimedMembers' => $this->claimedMembers,
            'redeemedMembers' => $this->redeemedMembers,
            'qrCodeImage' => $qrCodeImage,
        ])->layout('components.layouts.app');
    }
}
