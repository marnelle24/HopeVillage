<?php

namespace App\Livewire\Member;

use Livewire\Component;

class PointsHeader extends Component
{
    protected $listeners = [
        'points-updated' => 'refreshPoints',
    ];

    public function refreshPoints()
    {
        // Refresh user to get latest points, then refresh component
        $user = auth()->user();
        if ($user) {
            $user->refresh();
        }
        $this->dispatch('$refresh');
    }

    public function reload()
    {
        // Manual reload button functionality - refresh user data
        $this->refreshPoints();
    }

    public function getTotalPointsProperty()
    {
        $user = auth()->user();
        if (!$user) {
            return 0;
        }
        
        return $user->total_points ?? 0;
    }

    public function render()
    {
        return view('livewire.member.points-header');
    }
}
