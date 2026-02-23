<?php

namespace App\Livewire\Announcements;

use App\Models\Announcement;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public $announcementId;

    public $title = '';

    public $body = '';

    public $status = 'draft';

    public $published_at = '';

    public $starts_at = '';

    public $ends_at = '';

    public $link_url = '';

    public $visibility = 'all';

    public $banner;

    public $existingBanner = null;

    public $showMessage = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'body' => 'nullable|string',
        'status' => 'required|in:draft,published',
        'published_at' => 'nullable|date',
        'starts_at' => 'nullable|date',
        'ends_at' => 'nullable|date|after_or_equal:starts_at',
        'link_url' => 'nullable|url|max:2048',
        'visibility' => 'required|in:members,merchants,members_and_merchants,all',
        'banner' => 'nullable|image|max:2048',
    ];

    public function mount(?int $id = null): void
    {
        $this->announcementId = $id;
        $this->showMessage = session()->has('message');

        if ($this->announcementId) {
            $announcement = Announcement::findOrFail($this->announcementId);
            $this->title = $announcement->title;
            $this->body = $announcement->body ?? '';
            $this->status = $announcement->status;
            $this->published_at = $announcement->published_at?->format('Y-m-d\TH:i') ?? '';
            $this->starts_at = $announcement->starts_at?->format('Y-m-d\TH:i') ?? '';
            $this->ends_at = $announcement->ends_at?->format('Y-m-d\TH:i') ?? '';
            $this->link_url = $announcement->link_url ?? '';
            $this->visibility = $announcement->visibility ?? 'all';
            $media = $announcement->getFirstMedia('banner');
            if ($media) {
                $this->existingBanner = $media->getUrl();
            }
        }
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function updatedPublishedAt(): void
    {
        $this->status = trim((string) $this->published_at) !== '' ? 'published' : 'draft';
    }

    public function save()
    {
        $this->validate();

        $publishedAt = trim((string) $this->published_at) !== '' ? $this->published_at : null;
        $startsAt = trim((string) $this->starts_at) !== '' ? $this->starts_at : null;
        $endsAt = trim((string) $this->ends_at) !== '' ? $this->ends_at : null;
        $status = $publishedAt !== null ? 'published' : 'draft';

        $data = [
            'title' => $this->title,
            'body' => $this->body ?: null,
            'status' => $status,
            'published_at' => $publishedAt,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'link_url' => trim((string) $this->link_url) !== '' ? $this->link_url : null,
            'visibility' => $this->visibility,
            'created_by' => auth()->id(),
        ];

        if ($this->announcementId) {
            $announcement = Announcement::findOrFail($this->announcementId);
            $announcement->update($data);
            $message = 'Announcement updated successfully.';
        } else {
            $announcement = Announcement::create($data);
            $message = 'Announcement created successfully.';
        }

        if ($this->banner) {
            $announcement->clearMediaCollection('banner');
            $announcement->addMedia($this->banner->getRealPath())
                ->usingName($announcement->title . ' - Banner')
                ->toMediaCollection('banner');
        }

        session()->flash('message', $message);
        $this->showMessage = true;

        return redirect()->route('admin.announcements.index');
    }

    public function removeBanner(): void
    {
        if ($this->announcementId) {
            $announcement = Announcement::findOrFail($this->announcementId);
            $announcement->clearMediaCollection('banner');
            $this->existingBanner = null;
        }
        $this->banner = null;
    }

    public function render()
    {
        return view('livewire.announcements.form')->layout('components.layouts.app');
    }
}
