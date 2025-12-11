<?php

namespace App\Livewire\Merchant;

use Livewire\Component;

class MerchantSwitcherDropdown extends Component
{
    public $selectedMerchantId;

    public function mount()
    {
        $user = auth()->user();
        $currentMerchant = $user->currentMerchant();
        $this->selectedMerchantId = $currentMerchant ? $currentMerchant->id : null;
    }

    public function switchMerchant($merchantId = null)
    {
        $merchantId = $merchantId ?? $this->selectedMerchantId;
        $user = auth()->user();
        
        if ($user->setCurrentMerchant($merchantId)) {
            $this->selectedMerchantId = $merchantId;
            session()->flash('merchant-switched', 'Merchant switched successfully.');
            return redirect(request()->header('Referer') ?? route('merchant.dashboard'));
        }
        
        session()->flash('merchant-switch-error', 'Failed to switch merchant.');
    }

    public function render()
    {
        $user = auth()->user();
        $merchants = $user->merchants;
        $currentMerchant = $user->currentMerchant();

        return view('livewire.merchant.merchant-switcher-dropdown', [
            'merchants' => $merchants,
            'currentMerchant' => $currentMerchant,
        ]);
    }
}
