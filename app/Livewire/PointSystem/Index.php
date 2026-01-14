<?php

namespace App\Livewire\PointSystem;

use App\Models\ActivityType;
use App\Models\PointSystemConfig;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $activityTypeFilter = '';
    public $showMessage = false;
    public $pointSystemEnabled = true;

    protected $paginationTheme = 'tailwind';
    
    protected $listeners = ['point-system-toggled' => 'updatePointSystemState'];

    public function mount()
    {
        $this->showMessage = session()->has('message');
        $this->pointSystemEnabled = (bool) Setting::get('point_system_enabled', true);
    }

    public function updatePointSystemState($enabled)
    {
        // Ensure we're getting a boolean value
        $this->pointSystemEnabled = (bool) $enabled;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingActivityTypeFilter()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        return redirect()->route('admin.point-system.edit', $id);
    }

    public function delete($id)
    {
        $config = PointSystemConfig::findOrFail($id);
        $config->delete();
        
        session()->flash('message', 'Point System configuration deleted successfully.');
        $this->showMessage = true;
        $this->dispatch('point-system-deleted');
    }

    public function render()
    {
        $query = PointSystemConfig::with(['activityType', 'location', 'amenity']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('points', 'like', '%' . $this->search . '%')
                  ->orWhereHas('activityType', function ($q) {
                      $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        if ($this->activityTypeFilter) {
            $query->where('activity_type_id', $this->activityTypeFilter);
        }

        $configs = $query->orderBy('created_at', 'desc')->paginate(5);
        $activityTypes = ActivityType::orderBy('name')->get();

        return view('livewire.point-system.index', [
            'configs' => $configs,
            'activityTypes' => $activityTypes,
        ])->layout('components.layouts.app');
    }
}

