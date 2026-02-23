<?php

namespace App\Livewire;

use App\Models\Announcement;
use Illuminate\Support\Collection;
use Livewire\Component;

class AnnouncementLightbox extends Component
{
    /**
     * Active announcements that have a banner image (for carousel).
     *
     * @return Collection<int, Announcement>
     */
    public function getAnnouncementsWithBannerProperty(): Collection
    {
        return Announcement::active()
            ->visibleTo(auth()->user())
            ->whereHas('media', fn ($q) => $q->where('collection_name', 'banner'))
            ->latest('published_at')
            ->get();
    }

    public function render()
    {
        $announcements = $this->announcementsWithBanner;

        return view('livewire.announcement-lightbox', [
            'announcements' => $announcements,
        ]);
    }
}
