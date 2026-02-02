<?php

namespace App\Livewire\Member\News;

use App\Models\News;
use Livewire\Component;

class Profile extends Component
{
    public News $news;

    public function mount(string $slug): void
    {
        $news = News::query()
            ->published()
            ->where('slug', $slug)
            ->with(['categories', 'creator'])
            ->firstOrFail();

        $this->news = $news;
    }

    public function render()
    {
        return view('livewire.member.news.profile', [
            'news' => $this->news,
        ])->layout('layouts.app', [
            'title' => $this->news->title,
        ]);
    }
}
