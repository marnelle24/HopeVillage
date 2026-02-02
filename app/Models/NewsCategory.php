<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class NewsCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_news_category');
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (NewsCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function (NewsCategory $category) {
            if ($category->isDirty('name') && ! $category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
