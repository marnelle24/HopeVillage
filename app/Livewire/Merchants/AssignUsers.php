<?php

namespace App\Livewire\Merchants;

use App\Models\Merchant;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AssignUsers extends Component
{
    use WithPagination;

    public $merchantCode;
    public $merchant;
    public $showModal = false;
    public $search = '';
    public $selectedUserIds = [];

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['open-assign-users-modal' => 'openModal', 'refresh-users' => 'loadMerchant'];

    public function mount($merchant_code)
    {
        $this->merchantCode = $merchant_code;
        $this->loadMerchant();
    }

    public function loadMerchant()
    {
        $this->merchant = Merchant::with(['users'])->where('merchant_code', $this->merchantCode)->firstOrFail();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->search = '';
        $this->selectedUserIds = [];
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->search = '';
        $this->selectedUserIds = [];
    }

    public function toggleUser($userId)
    {
        if (in_array($userId, $this->selectedUserIds)) {
            $this->selectedUserIds = array_filter($this->selectedUserIds, fn($id) => $id != $userId);
        } else {
            $this->selectedUserIds[] = $userId;
        }
    }

    public function assignSelectedUsers()
    {
        $assignedCount = count($this->selectedUserIds);
        
        foreach ($this->selectedUserIds as $userId) {
            $user = User::findOrFail($userId);
            
            // Attach user to merchant if not already attached
            if (!$this->merchant->users()->where('user_id', $userId)->exists()) {
                // If this is the user's first merchant, set it as default and current
                $isFirstMerchant = $user->merchants()->count() === 0;
                
                $this->merchant->users()->attach($userId, [
                    'is_default' => $isFirstMerchant,
                ]);

                if ($isFirstMerchant) {
                    $user->update(['current_merchant_id' => $this->merchant->id]);
                }
            }
        }

        $this->selectedUserIds = [];
        $this->loadMerchant();
        session()->flash('users-assigned', $assignedCount . ' user(s) assigned successfully.');
        $this->dispatch('refresh-users');
    }

    public function render()
    {
        // Get merchant user IDs already assigned to this merchant
        $assignedUserIds = $this->merchant->users->pluck('id')->toArray();

        // Search for merchant users not yet assigned to this merchant
        $query = User::where('user_type', 'merchant_user')
            ->whereNotIn('id', $assignedUserIds);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $availableUsers = $query->orderBy('name')->paginate(10);

        return view('livewire.merchants.assign-users', [
            'merchant' => $this->merchant,
            'availableUsers' => $availableUsers,
        ]);
    }
}
