<?php

namespace App\Livewire\PointSystem;

use App\Models\Setting;
use Livewire\Component;

class Toggle extends Component
{
    public $pointSystemEnabled = true;

    public function mount()
    {
        $this->pointSystemEnabled = (bool) Setting::get('point_system_enabled', true);
    }

    public function togglePointSystem()
    {
        $this->pointSystemEnabled = !$this->pointSystemEnabled;
        Setting::set('point_system_enabled', $this->pointSystemEnabled ? '1' : '0');
        
        // Dispatch event to refresh parent component if needed
        $this->dispatch('point-system-toggled', enabled: $this->pointSystemEnabled);
    }

    public function render()
    {
        return view('livewire.point-system.toggle');
    }
}
