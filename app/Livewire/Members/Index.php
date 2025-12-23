<?php

namespace App\Livewire\Members;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $verifiedFilter = 'all'; // all | verified | unverified
    public bool $showMessage = false;

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->showMessage = session()->has('message') || session()->has('error');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingVerifiedFilter(): void
    {
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

        $member = User::where('id', $userId)
            ->where('user_type', 'member')
            ->firstOrFail();

        $member->delete();

        session()->flash('message', 'Member deleted successfully.');
        $this->showMessage = true;
    }

    public function render()
    {
        $query = User::query()->where('user_type', 'member');

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

        $members = $query
            ->withCount(['memberActivities', 'eventRegistrations'])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('livewire.members.index', [
            'members' => $members,
        ])->layout('components.layouts.app');
    }
}


