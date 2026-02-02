<?php

namespace App\Livewire\Member\News;

use App\Models\News;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = News::query()
            ->published()
            ->with(['categories']);

        if ($this->search !== '') {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', $s)
                    ->orWhere('body', 'like', $s);
            });
        }

        $news = $query
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('livewire.member.news.index', [
            'news' => $news,
        ])->layout('layouts.app', [
            'title' => 'News',
        ]);
    }
}
