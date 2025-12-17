<?php

namespace App\Livewire\Members;

use App\Models\User;
use Livewire\Component;

class Profile extends Component
{
    public string $fin;
    public User $member;

    public function mount(string $fin): void
    {
        $this->fin = $fin;
        $this->loadMember();
    }

    public function loadMember(): void
    {
        $this->member = User::query()
            ->where('user_type', 'member')
            ->where('fin', $this->fin)
            ->with([
                'memberActivities' => function ($q) {
                    $q->with(['activityType', 'location', 'pointLog'])
                        ->latest('activity_time')
                        ->limit(25);
                },
                'pointLogs' => function ($q) {
                    $q->with(['activityType', 'location'])
                        ->latest('awarded_at')
                        ->limit(25);
                },
                'eventRegistrations' => function ($q) {
                    $q->with('event.location')
                        ->latest('registered_at')
                        ->limit(10);
                },
                'vouchers' => function ($q) {
                    $q->latest('user_voucher.claimed_at')->limit(10);
                },
            ])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.members.profile', [
            'member' => $this->member,
        ])->layout('components.layouts.app');
    }
}


