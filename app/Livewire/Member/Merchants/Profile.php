<?php

namespace App\Livewire\Member\Merchants;

use App\Models\Merchant;
use Livewire\Component;

class Profile extends Component
{
    public Merchant $merchant;

    public function mount(string $merchant_code): void
    {
        $this->merchant = Merchant::query()
            ->where('is_active', true)
            ->where('merchant_code', $merchant_code)
            ->with('media')
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.member.merchants.profile', [
            'merchant' => $this->merchant,
        ]);
    }
}
