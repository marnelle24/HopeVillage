<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Announcement extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'body',
        'status',
        'published_at',
        'starts_at',
        'ends_at',
        'link_url',
        'visibility',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Announcement $announcement) {
            if (empty($announcement->created_by) && auth()->check()) {
                $announcement->created_by = auth()->id();
            }
        });
    }

    /**
     * Scope to announcements visible to the given user.
     * Unauthenticated users see no announcements.
     */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        $allowedVisibilities = match ($user->user_type) {
            'member' => ['members', 'members_and_merchants', 'all'],
            'merchant_user' => ['merchants', 'members_and_merchants', 'all'],
            'admin' => ['all'],
            default => [],
        };

        return $query->whereIn('visibility', $allowedVisibilities);
    }

    /**
     * Scope to only active announcements (published and within optional date window).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * Register media collections for banner image.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Get the banner image URL.
     */
    public function getBannerUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('banner');
        return $media ? $media->getUrl() : null;
    }
}
