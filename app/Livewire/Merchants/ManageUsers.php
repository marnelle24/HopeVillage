<?php

namespace App\Livewire\Merchants;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class ManageUsers extends Component
{
    use WithPagination;

    public $merchantCode;
    public $merchant;
    public $showModal = false;
    public $activeTab = 'create'; // 'create' or 'assign'
    
    // Form fields for creating user
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $showAddForm = false;
    
    // Fields for assigning existing users
    public $search = '';
    public $selectedUserIds = [];

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email|max:255',
        'password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'email.unique' => 'This email is already registered.',
        'password.confirmed' => 'The password confirmation does not match.',
    ];

    protected $listeners = ['open-manage-users-modal' => 'openModal', 'refresh-users' => 'loadMerchant'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        if ($tab === 'create') {
            $this->resetForm();
            $this->showAddForm = false;
        } else {
            $this->selectedUserIds = [];
            $this->search = '';
        }
    }

    public function mount($merchant_code)
    {
        $this->merchantCode = $merchant_code;
        $this->loadMerchant();
    }

    public function loadMerchant()
    {
        $this->merchant = Merchant::with(['users'])->where('merchant_code', $this->merchantCode)->firstOrFail();
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
        $this->activeTab = 'list'; // Switch to list tab after assignment
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->activeTab = 'list';
        $this->resetForm();
        $this->showAddForm = false;
        $this->selectedUserIds = [];
        $this->search = '';
        $this->resetPage();
        $this->loadMerchant();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showAddForm = false;
        $this->resetForm();
    }

    public function showAddUserForm()
    {
        $this->showAddForm = true;
        $this->resetForm();
    }

    public function cancelAddUser()
    {
        $this->showAddForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetErrorBag();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function createUser()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'user_type' => 'merchant_user',
            'current_merchant_id' => $this->merchant->id,
        ]);

        // Attach user to merchant and set as default
        $this->merchant->users()->attach($user->id, [
            'is_default' => true,
        ]);

        $this->resetForm();
        $this->showAddForm = false;
        $this->loadMerchant();
        
        session()->flash('user-created', 'Merchant user created successfully.');
        $this->dispatch('refresh-users');
        $this->activeTab = 'list'; // Switch to list tab after creation
    }

    public function removeUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Check if this was the default merchant before detaching
        $pivot = DB::table('merchant_user')
            ->where('user_id', $userId)
            ->where('merchant_id', $this->merchant->id)
            ->first();
        
        $wasDefault = $pivot && $pivot->is_default;
        $wasCurrent = $user->current_merchant_id == $this->merchant->id;
        
        // Detach user from merchant
        $this->merchant->users()->detach($userId);

        // Reload user relationships to get updated merchants list
        $user->refresh();
        $user->load('merchants');

        // If this was the current merchant, set a new current merchant
        if ($wasCurrent) {
            $defaultMerchant = $user->defaultMerchant();
            if ($defaultMerchant) {
                $user->update(['current_merchant_id' => $defaultMerchant->id]);
                session(['current_merchant_id' => $defaultMerchant->id]);
            } else {
                // Get first remaining merchant
                $firstMerchant = $user->merchants()->first();
                if ($firstMerchant) {
                    $user->update(['current_merchant_id' => $firstMerchant->id]);
                    session(['current_merchant_id' => $firstMerchant->id]);
                    // Set as default if this was the default
                    if ($wasDefault) {
                        $user->merchants()->updateExistingPivot($firstMerchant->id, ['is_default' => true]);
                    }
                } else {
                    // No merchants left, clear current_merchant_id
                    $user->update(['current_merchant_id' => null]);
                    session()->forget('current_merchant_id');
                }
            }
        }

        // If this was the default merchant and there are remaining merchants, set a new default
        if ($wasDefault) {
            $firstRemaining = $user->merchants()->first();
            if ($firstRemaining) {
                $user->merchants()->updateExistingPivot($firstRemaining->id, ['is_default' => true]);
            }
        }

        $this->loadMerchant();
        
        session()->flash('user-removed', 'User removed from merchant successfully.');
        $this->dispatch('refresh-users');
    }

    public function render()
    {
        $availableUsers = collect();
        
        if ($this->activeTab === 'assign') {
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
        }

        return view('livewire.merchants.manage-users', [
            'merchant' => $this->merchant,
            'availableUsers' => $availableUsers,
        ]);
    }
}
