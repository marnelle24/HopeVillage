<?php

namespace App\Livewire\Member;

use App\Models\MemberActivity;
use Livewire\Component;
use Livewire\WithPagination;

class Activities extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateFilter = 'all'; // all | today | week | month

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $query = MemberActivity::query()
            ->where('user_id', $user->id)
            ->with(['activityType', 'location', 'amenity', 'pointLog']);

        // Search filter
        if ($this->search !== '') {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', $s)
                    ->orWhereHas('activityType', function ($typeQuery) use ($s) {
                        $typeQuery->where('name', 'like', $s)
                            ->orWhere('description', 'like', $s);
                    })
                    ->orWhereHas('location', function ($locQuery) use ($s) {
                        $locQuery->where('name', 'like', $s);
                    });
            });
        }

        // Date filter
        if ($this->dateFilter === 'today') {
            $query->whereDate('activity_time', today());
        } elseif ($this->dateFilter === 'week') {
            $query->where('activity_time', '>=', now()->subWeek());
        } elseif ($this->dateFilter === 'month') {
            $query->where('activity_time', '>=', now()->subMonth());
        }

        $activities = $query
            ->orderByDesc('activity_time')
            ->paginate(15);

        return view('livewire.member.activities', [
            'activities' => $activities,
        ])->layout('layouts.app', [
            'title' => 'My Activities'
        ]);
    }
}
