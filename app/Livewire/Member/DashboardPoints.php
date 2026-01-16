<?php

namespace App\Livewire\Member;

use Livewire\Component;

class DashboardPoints extends Component
{
    public $previousPoints = null;

    protected $listeners = [
        'points-updated' => 'handlePointsUpdate',
    ];

    public function mount()
    {
        $user = auth()->user();
        if ($user) {
            $user->refresh();
            $this->previousPoints = $user->total_points ?? 0;
        }
    }

    public function handlePointsUpdate()
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }
        
        // Refresh the user model to get the latest points
        $user->refresh();
        $currentPoints = $user->total_points ?? 0;
        
        // Check if points have changed
        if ($this->previousPoints !== null && $this->previousPoints != $currentPoints) {
            // Dispatch toast notification
            $this->dispatch('notify', type: 'success', message: 'Points updated');
            
            // Update previous points
            $this->previousPoints = $currentPoints;
        }
        
        // Trigger re-render
        $this->dispatch('$refresh');
    }

    public function getTotalPointsProperty()
    {
        $user = auth()->user();
        if (!$user) {
            return 0;
        }
        
        // Refresh the user model to get the latest points
        $user->refresh();
        
        return $user->total_points ?? 0;
    }

    public function render()
    {
        // Check for points changes on each render (including polling)
        $user = auth()->user();
        if ($user) {
            $user->refresh();
            $currentPoints = $user->total_points ?? 0;
            
            // Check if points have changed (for polling updates)
            if ($this->previousPoints !== null && $this->previousPoints != $currentPoints) {
                // Dispatch toast notification
                $this->dispatch('notify', type: 'success', message: 'Points updated');
                
                // Update previous points
                $this->previousPoints = $currentPoints;
            } elseif ($this->previousPoints === null) {
                // Initialize on first load
                $this->previousPoints = $currentPoints;
            }
        }
        
        return view('livewire.member.dashboard-points');
    }
}
