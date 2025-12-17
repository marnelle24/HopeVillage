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

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingVerifiedFilter(): void
    {
        $this->resetPage();
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


