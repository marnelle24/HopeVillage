<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MarketplaceItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'marketplace_category_id',
        'description',
        'points_cost',
        'per_item_quantity',
        'stock',
        'is_active',
        'valid_from',
        'valid_until',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'points_cost' => 'integer',
            'per_item_quantity' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function getImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('image');

        return $media ? $media->getUrl() : null;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MarketplaceCategory::class, 'marketplace_category_id');
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'marketplace_item_location')->withTimestamps();
    }

    public function orderLineItems(): HasMany
    {
        return $this->hasMany(MarketplaceOrderItem::class, 'marketplace_item_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
            });
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('stock')->orWhere('stock', '>', 0);
        });
    }

    public function scopeAvailableForMembers(Builder $query): Builder
    {
        return $query->active()->published()->inStock();
    }

    public function scopeAvailableAtLocation(Builder $query, ?int $locationId): Builder
    {
        if (! $locationId) {
            return $query;
        }

        return $query->where(function ($q) use ($locationId) {
            $q->whereDoesntHave('locations')
                ->orWhereHas('locations', function ($locationQuery) use ($locationId) {
                    $locationQuery->where('locations.id', $locationId);
                });
        });
    }

    public function isAvailableForPurchase(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();
        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }
        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }
        if ($this->stock !== null && $this->stock <= 0) {
            return false;
        }

        return true;
    }

    public function hasStockFor(int $quantity): bool
    {
        if ($this->stock === null) {
            return true;
        }

        return $this->stock >= $quantity;
    }
}
