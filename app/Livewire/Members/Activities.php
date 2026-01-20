<?php

namespace App\Livewire\Members;

use App\Models\ActivityType;
use App\Models\Location;
use App\Models\MemberActivity;
use Livewire\Component;
use Livewire\WithPagination;

class Activities extends Component
{
    use WithPagination;

    public string $search = '';
    public string $locationFilter = '';
    public string $activityTypeFilter = '';
    public string $dateFilter = 'all'; // all | today | week | month

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingLocationFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActivityTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = MemberActivity::query()
            ->with(['user', 'activityType', 'location', 'amenity', 'pointLog']);

        // Search filter
        if ($this->search !== '') {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', function ($userQuery) use ($s) {
                    $userQuery->where('name', 'like', $s)
                        ->orWhere('email', 'like', $s)
                        ->orWhere('fin', 'like', $s);
                })
                ->orWhere('description', 'like', $s);
            });
        }

        // Location filter
        if ($this->locationFilter !== '') {
            $query->where('location_id', $this->locationFilter);
        }

        // Activity type filter
        if ($this->activityTypeFilter !== '') {
            $query->where('activity_type_id', $this->activityTypeFilter);
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

        // Get filter options
        $locations = Location::orderBy('name')->get(['id', 'name']);

        $activityTypes = ActivityType::orderBy('name')->get(['id', 'description']);

        return view('livewire.members.activities', [
            'activities' => $activities,
            'locations' => $locations,
            'activityTypes' => $activityTypes,
        ])->layout('components.layouts.app');
    }
}

