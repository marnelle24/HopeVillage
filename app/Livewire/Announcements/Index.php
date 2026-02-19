<?php

namespace App\Livewire\Announcements;

use App\Models\Announcement;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 'all';

    public $showMessage = false;

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->showMessage = session()->has('message');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        session()->flash('message', 'Announcement deleted successfully.');
        $this->showMessage = true;
    }

    public function render()
    {
        $query = Announcement::with(['creator', 'media']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('body', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('livewire.announcements.index', [
            'announcements' => $announcements,
        ])->layout('components.layouts.app');
    }
}
