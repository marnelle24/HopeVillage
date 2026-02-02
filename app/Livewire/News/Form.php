<?php

namespace App\Livewire\News;

use App\Models\News as NewsModel;
use App\Models\NewsCategory;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public $newsId;

    public $title = '';

    public $slug = '';

    public $body = '';

    public $status = 'draft';

    public $published_at = '';

    public $categoryIds = [];

    public $thumbnail;

    public $existingThumbnail = null;

    public $showMessage = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
        'body' => 'nullable|string',
        'status' => 'required|in:draft,published',
        'published_at' => 'nullable|date',
        'categoryIds' => 'nullable|array',
        'categoryIds.*' => 'exists:news_categories,id',
        'thumbnail' => 'nullable|image|max:2048',
    ];

    public function mount(?int $id = null): void
    {
        $this->newsId = $id;
        $this->showMessage = session()->has('message');

        if ($this->newsId) {
            $news = NewsModel::with('categories')->findOrFail($this->newsId);
            $this->title = $news->title;
            $this->slug = $news->slug ?? '';
            $this->body = $news->body ?? '';
            $this->status = $news->status;
            $this->published_at = $news->published_at?->format('Y-m-d\TH:i') ?? '';
            $this->categoryIds = $news->categories->pluck('id')->all();
            $media = $news->getFirstMedia('thumbnail');
            if ($media) {
                $this->existingThumbnail = $media->getUrl();
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
        $status = $publishedAt !== null ? 'published' : 'draft';

        $data = [
            'title' => $this->title,
            'slug' => $this->slug ?: null,
            'body' => $this->body ?: null,
            'status' => $status,
            'published_at' => $publishedAt,
            'created_by' => auth()->id(),
        ];

        if ($this->newsId) {
            $news = NewsModel::findOrFail($this->newsId);
            $news->update($data);
            $message = 'News article updated successfully.';
        } else {
            $news = NewsModel::create($data);
            $message = 'News article created successfully.';
        }

        $news->categories()->sync($this->categoryIds);

        if ($this->thumbnail) {
            $news->clearMediaCollection('thumbnail');
            $news->addMedia($this->thumbnail->getRealPath())
                ->usingName($news->title . ' - Thumbnail')
                ->toMediaCollection('thumbnail');
        }

        session()->flash('message', $message);
        $this->showMessage = true;

        return redirect()->route('admin.news.index');
    }

    public function removeThumbnail(): void
    {
        if ($this->newsId) {
            $news = NewsModel::findOrFail($this->newsId);
            $news->clearMediaCollection('thumbnail');
            $this->existingThumbnail = null;
        }
        $this->thumbnail = null;
    }

    public function render()
    {
        $categories = NewsCategory::orderBy('name')->get();

        return view('livewire.news.form', [
            'categories' => $categories,
        ])->layout('components.layouts.app');
    }
}
