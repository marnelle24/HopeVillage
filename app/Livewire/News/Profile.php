<?php

namespace App\Livewire\News;

use App\Models\News as NewsModel;
use Livewire\Component;

class Profile extends Component
{
    public $newsId;

    public $news;

    public function mount(int $id): void
    {
        $this->newsId = $id;
        $this->news = NewsModel::with(['creator', 'categories', 'media'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.news.profile', [
            'news' => $this->news,
        ])->layout('components.layouts.app');
    }
}
