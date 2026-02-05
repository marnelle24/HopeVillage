<?php

namespace App\Livewire\Members;

use App\Models\ActivityType;
use App\Models\MemberActivity;
use Livewire\Component;
use Livewire\WithPagination;

class Activities extends Component
{
    use WithPagination;

    public string $search = '';
    public string $activityTypeFilter = '';
    public string $dateFilter = 'all'; // all | today | week | month

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
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

    /**
     * Build the filtered query (shared by table and CSV export).
     */
    private function getFilteredQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = MemberActivity::query()
            ->with(['user', 'activityType', 'pointLog']);

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

        if ($this->activityTypeFilter !== '') {
            $query->where('activity_type_id', $this->activityTypeFilter);
        }

        if ($this->dateFilter === 'today') {
            $query->whereDate('activity_time', today());
        } elseif ($this->dateFilter === 'week') {
            $query->where('activity_time', '>=', now()->subWeek());
        } elseif ($this->dateFilter === 'month') {
            $query->where('activity_time', '>=', now()->subMonth());
        }

        return $query->orderByDesc('activity_time');
    }

    public function exportCsv()
    {
        return $this->redirect(route('admin.members.activities.export', [
            'search' => $this->search,
            'activity_type' => $this->activityTypeFilter,
            'date' => $this->dateFilter,
        ]));
    }

    public function render()
    {
        $activities = $this->getFilteredQuery()->paginate(15);

        $activityTypes = ActivityType::orderBy('name')->get(['id', 'description']);

        return view('livewire.members.activities', [
            'activities' => $activities,
            'activityTypes' => $activityTypes,
        ])->layout('components.layouts.app');
    }
}

