<?php

namespace App\Livewire\News;

use App\Models\News as NewsModel;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = 'all';

    public $categoryFilter = '';

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

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $news = NewsModel::findOrFail($id);
        $news->delete();
        session()->flash('message', 'News article deleted successfully.');
        $this->showMessage = true;
    }

    public function render()
    {
        $query = NewsModel::with(['creator', 'categories', 'media']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('body', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter) {
            $query->whereHas('categories', function ($q) {
                $q->where('news_categories.id', $this->categoryFilter);
            });
        }

        $news = $query->orderBy('created_at', 'desc')->paginate(12);

        $categories = \App\Models\NewsCategory::orderBy('name')->get();

        return view('livewire.news.index', [
            'news' => $news,
            'categories' => $categories,
        ])->layout('components.layouts.app');
    }
}
