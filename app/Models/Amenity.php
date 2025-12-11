<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Amenity extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'location_id',
        'name',
        'description',
        'type',
        'capacity',
        'hourly_rate',
        'operating_hours',
        'is_bookable',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'operating_hours' => 'array',
            'is_bookable' => 'boolean',
            'is_active' => 'boolean',
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function memberActivities(): HasMany
    {
        return $this->hasMany(MemberActivity::class);
    }

    public function pointLogs(): HasMany
    {
        return $this->hasMany(PointLog::class);
    }

    /**
     * Register media collections for amenity images
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Get the thumbnail URL (first image)
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('images');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get the formatted type for display
     */
    public function getFormattedTypeAttribute(): string
    {
        if (str_starts_with($this->type, 'others-')) {
            $parts = explode('-', $this->type, 2);
            return $parts[1] ?? $this->type;
        }
        return $this->type;
    }

    /**
     * Get the display type (for badges/colors)
     */
    public function getDisplayTypeAttribute(): string
    {
        if (str_starts_with($this->type, 'others-')) {
            return 'Others';
        }
        return $this->type;
    }
}
