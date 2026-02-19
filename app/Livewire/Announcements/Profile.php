<?php

namespace App\Livewire\Announcements;

use App\Models\Announcement;
use Livewire\Component;

class Profile extends Component
{
    public $announcementId;

    public $announcement;

    public function mount(int $id): void
    {
        $this->announcementId = $id;
        $this->announcement = Announcement::with(['creator', 'media'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.announcements.profile', [
            'announcement' => $this->announcement,
        ])->layout('components.layouts.app');
    }
}
