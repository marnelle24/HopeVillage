<?php

namespace App\Livewire\Merchants;

use App\Models\Merchant;
use App\Services\QrCodeService;
use Livewire\Component;

class Profile extends Component
{
    public $merchantCode;
    public $merchant;

    protected $listeners = ['refresh-users' => 'loadMerchant'];

    public function mount($merchant_code)
    {
        $this->merchantCode = $merchant_code;
        $this->loadMerchant();
    }

    public function loadMerchant()
    {
        $this->merchant = Merchant::with([
            'vouchers' => function ($query) {
                $query->latest();
            },
            'adminVouchers' => function ($query) {
                $query->latest();
            },
            'users'
        ])->where('merchant_code', $this->merchantCode)->firstOrFail();
    }

    public function render()
    {
        $qrCodeService = app(QrCodeService::class);
        $qrCodeImage = $qrCodeService->generateQrCodeImage($this->merchant->merchant_code, 400);

        return view('livewire.merchants.profile', [
            'merchant' => $this->merchant,
            'qrCodeImage' => $qrCodeImage,
        ])->layout('components.layouts.app');
    }
}
