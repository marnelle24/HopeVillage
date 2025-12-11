<?php

namespace App\Livewire\Merchants;

use App\Models\Merchant;
use Livewire\Component;

class Profile extends Component
{
    public $merchantCode;
    public $merchant;

    public function mount($merchant_code)
    {
        $this->merchantCode = $merchant_code;
        $this->loadMerchant();
    }

    public function loadMerchant()
    {
        $this->merchant = Merchant::with(['vouchers' => function ($query) {
            $query->latest()->take(10);
        }])->where('merchant_code', $this->merchantCode)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.merchants.profile', [
            'merchant' => $this->merchant,
        ])->layout('components.layouts.app');
    }
}
