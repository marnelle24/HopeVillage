<?php

namespace App\Livewire\NewsCategories;

use App\Models\NewsCategory;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

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

    public function delete(int $id): void
    {
        $category = NewsCategory::findOrFail($id);
        $category->news()->detach();
        $category->delete();
        session()->flash('message', 'Category deleted successfully.');
        $this->showMessage = true;
    }

    public function render()
    {
        $query = NewsCategory::withCount('news');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
        }

        $categories = $query->orderBy('name')->paginate(15);

        return view('livewire.news-categories.index', [
            'categories' => $categories,
        ])->layout('components.layouts.app');
    }
}
