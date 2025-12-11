<?php

namespace App\Livewire\Merchant;

use Livewire\Component;

class MerchantSwitcher extends Component
{
    public $selectedMerchantId;

    public function mount()
    {
        $user = auth()->user();
        $currentMerchant = $user->currentMerchant();
        $this->selectedMerchantId = $currentMerchant ? $currentMerchant->id : null;
    }

    public function switchMerchant()
    {
        $user = auth()->user();
        
        if ($user->setCurrentMerchant($this->selectedMerchantId)) {
            session()->flash('merchant-switched', 'Merchant switched successfully.');
            return redirect()->route('merchant.dashboard');
        }
        
        session()->flash('merchant-switch-error', 'Failed to switch merchant.');
    }

    public function render()
    {
        $user = auth()->user();
        $merchants = $user->merchants;
        $currentMerchant = $user->currentMerchant();

        return view('livewire.merchant.merchant-switcher', [
            'merchants' => $merchants,
            'currentMerchant' => $currentMerchant,
        ]);
    }
}
