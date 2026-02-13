<?php

namespace App\Livewire\Members;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use PasswordValidationRules;

    public string $search = '';
    public string $verifiedFilter = 'all'; // all | verified | unverified
    public string $userTypeFilter = 'all'; // all | member | merchant_user | admin
    public string $dateSort = 'desc'; // desc | asc
    public bool $showMessage = false;
    
    // Password reset properties
    public ?int $selectedUserId = null;
    public string $password = '';
    public string $password_confirmation = '';
    public bool $showPasswordReset = false;

    /** @var int|null ID of member selected for "Update Email Address" modal. */
    public ?int $updateEmailUserId = null;

    protected $paginationTheme = 'tailwind';

    protected $listeners = [
        'updateEmailModalClosed' => 'closeUpdateEmailModal',
    ];

    public function mount(): void
    {
        $this->showMessage = session()->has('message') || session()->has('error');
        
        // Check for password reset action in URL
        $action = request()->query('action');
        $userId = request()->query('userid');
        
        if ($action === 'password-reset' && $userId) {
            $this->selectedUserId = (int) $userId;
            $this->showPasswordReset = true;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingVerifiedFilter(): void
    {
        $this->resetPage();
    }

    public function updatingUserTypeFilter(): void
    {
        $this->resetPage();
    }

    public function sortByDate(): void
    {
        // Toggle between desc and asc
        $this->dateSort = $this->dateSort === 'desc' ? 'asc' : 'desc';
        $this->resetPage();
    }

    public function delete($userId): void
    {
        
        // Only allow admin users to delete members
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'You do not have permission to delete members.');
            $this->showMessage = true;
            return;
        }

        $member = User::where('id', $userId)->firstOrFail();

        // Capture member details before deletion
        $memberDetails = [
            'id' => $member->id,
            'name' => $member->name,
            'email' => $member->email,
            'fin' => $member->fin,
        ];

        $member->delete(); // This will now perform a soft delete

        // Get admin user details
        $adminUser = auth()->user();
        
        session()->flash('message', 'Member has been deleted successfully. The member can be restored if needed.');
        $this->showMessage = true;

        Log::info('Member soft deleted successfully', [
            'admin_user' => [
                'id' => $adminUser->id,
                'name' => $adminUser->name,
                'email' => $adminUser->email,
            ],
            'deleted_member' => $memberDetails,
            'deleted_at' => now()->toIso8601String(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function openUpdateEmailModal(int $userId): void
    {
        if (! auth()->user()->isAdmin()) {
            session()->flash('error', 'You do not have permission to update member email.');
            $this->showMessage = true;
            return;
        }
        $this->updateEmailUserId = $userId;

        // call toast message
        $this->dispatch('toast', message: 'Email update modal opened', type: 'success');
    }

    public function closeUpdateEmailModal(): void
    {
        $this->updateEmailUserId = null;
        if (session()->has('message') || session()->has('error')) {
            $this->showMessage = true;
        }
    }

    public function triggerPasswordReset($userId)
    {
        // Only allow admin users to reset passwords
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'You do not have permission to reset passwords.');
            $this->showMessage = true;
            return;
        }

        // Redirect with URL parameters
        return $this->redirect(route('admin.members.index', [
            'action' => 'password-reset',
            'userid' => $userId
        ]), navigate: true);
    }

    public function cancelPasswordReset()
    {
        $this->selectedUserId = null;
        $this->password = '';
        $this->password_confirmation = '';
        $this->showPasswordReset = false;
        
        // Redirect without URL parameters
        return $this->redirect(route('admin.members.index'), navigate: true);
    }

    public function resetPassword()
    {
        // Only allow admin users to reset passwords
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'You do not have permission to reset passwords.');
            $this->showMessage = true;
            return;
        }

        // Validate password
        Validator::make([
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ], [
            'password' => $this->passwordRules(),
        ])->validate();

        // Find the user
        $user = User::where('id', $this->selectedUserId)->firstOrFail();

        // Update password
        $user->forceFill([
            'password' => Hash::make($this->password),
        ])->save();

        // Log the password reset action
        $adminUser = auth()->user();
        Log::info('Member password reset by administrator', [
            'admin_user' => [
                'id' => $adminUser->id,
                'name' => $adminUser->name,
                'email' => $adminUser->email,
            ],
            'member_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'fin' => $user->fin,
            ],
            'reset_at' => now()->toIso8601String(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Reset form and show success message
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedUserId = null;
        $this->showPasswordReset = false;

        session()->flash('message', 'Password reset successfully.');
        $this->showMessage = true;

        // Redirect without URL parameters
        return $this->redirect(route('admin.members.index'), navigate: true);
    }

    public function render()
    {
        $query = User::query();

        // Apply user type filter
        if ($this->userTypeFilter !== 'all') {
            $query->where('user_type', $this->userTypeFilter);
        }

        if ($this->search !== '') {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                    ->orWhere('email', 'like', $s)
                    ->orWhere('fin', 'like', $s)
                    ->orWhere('whatsapp_number', 'like', $s);
            });
        }

        if ($this->verifiedFilter === 'verified') {
            $query->where('is_verified', true);
        } elseif ($this->verifiedFilter === 'unverified') {
            $query->where('is_verified', false);
        }

        // Apply date sorting
        if ($this->dateSort === 'asc') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderByDesc('created_at');
        }

        $members = $query->paginate(10);

        $selectedUser = null;
        if ($this->showPasswordReset && $this->selectedUserId) {
            $selectedUser = User::where('id', $this->selectedUserId)->first();
        }

        return view('livewire.members.index', [
            'members' => $members,
            'selectedUser' => $selectedUser,
        ])->layout('components.layouts.app');
    }
}


