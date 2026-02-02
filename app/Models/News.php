<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;

class News extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'status',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NewsCategory::class, 'news_news_category');
    }

    /**
     * Scope to only published news (visible to members).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (News $news) {
            if (empty($news->slug)) {
                $news->slug = static::uniqueSlug(Str::slug($news->title));
            }
            if (empty($news->created_by)) {
                $news->created_by = auth()->id();
            }
        });

        static::updating(function (News $news) {
            if ($news->isDirty('title') && ! $news->isDirty('slug')) {
                $news->slug = static::uniqueSlug(Str::slug($news->title), $news->id);
            }
        });
    }

    protected static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = $base;
        $count = 0;
        $query = static::query();
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        while ($query->clone()->where('slug', $slug)->exists()) {
            $count++;
            $slug = $base . '-' . $count;
        }
        return $slug;
    }

    /**
     * Register media collections for news thumbnail.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Get the thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('thumbnail');
        return $media ? $media->getUrl() : null;
    }
}
