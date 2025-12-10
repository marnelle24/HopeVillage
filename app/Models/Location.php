<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Location extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'is_active',
        'location_code',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function amenities(): HasMany
    {
        return $this->hasMany(Amenity::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
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
     * Register media collections for location thumbnails
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Get the thumbnail URL
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('thumbnail');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($location) {
            if (empty($location->location_code)) {
                $location->location_code = static::generateUniqueLocationCode();
            }
        });
    }

    /**
     * Generate a unique location code.
     *
     * @return string
     */
    protected static function generateUniqueLocationCode(): string
    {
        do {
            $code = 'LOC-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (static::where('location_code', $code)->exists());

        return $code;
    }
}
