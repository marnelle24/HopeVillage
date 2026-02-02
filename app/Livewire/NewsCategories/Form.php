<?php

namespace App\Livewire\NewsCategories;

use App\Models\NewsCategory;
use Livewire\Component;

class Form extends Component
{
    public $categoryId;

    public $name = '';

    public $slug = '';

    public $showMessage = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
    ];

    public function mount(?int $id = null): void
    {
        $this->categoryId = $id;
        $this->showMessage = session()->has('message');

        if ($this->categoryId) {
            $category = NewsCategory::findOrFail($this->categoryId);
            $this->name = $category->name;
            $this->slug = $category->slug ?? '';
        }
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        if ($this->categoryId) {
            $category = NewsCategory::findOrFail($this->categoryId);
            $category->update([
                'name' => $this->name,
                'slug' => $this->slug ?: null,
            ]);
            $message = 'Category updated successfully.';
        } else {
            NewsCategory::create([
                'name' => $this->name,
                'slug' => $this->slug ?: null,
            ]);
            $message = 'Category created successfully.';
        }

        session()->flash('message', $message);
        $this->showMessage = true;

        return redirect()->route('admin.news-categories.index');
    }

    public function render()
    {
        return view('livewire.news-categories.form')->layout('components.layouts.app');
    }
}
