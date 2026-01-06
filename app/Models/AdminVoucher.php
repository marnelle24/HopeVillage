<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AdminVoucher extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'voucher_code',
        'name',
        'description',
        'points_cost',
        'valid_from',
        'valid_until',
        'usage_limit',
        'usage_count',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'points_cost' => 'integer',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'usage_limit' => 'integer',
            'usage_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function merchants(): BelongsToMany
    {
        return $this->belongsToMany(Merchant::class, 'admin_voucher_merchant')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_admin_voucher')
            ->withPivot(['status', 'claimed_at', 'redeemed_at', 'redeemed_at_merchant_id'])
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($adminVoucher) {
            if (empty($adminVoucher->voucher_code)) {
                $adminVoucher->voucher_code = static::generateUniqueVoucherCode();
            }
        });
    }

    /**
     * Generate a unique voucher code.
     *
     * @return string
     */
    protected static function generateUniqueVoucherCode(): string
    {
        do {
            $code = 'AVOU-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (static::where('voucher_code', $code)->exists());

        return $code;
    }

    /**
     * Check if voucher is valid (active, within validity period, and not exceeded usage limit)
     */
    public function isValid(): bool
    {
        // First check if the voucher is active
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        // Check if voucher has started (valid_from)
        // If valid_from is set, current time must be >= valid_from
        if ($this->valid_from !== null) {
            $validFrom = $this->valid_from;
            // Ensure we have a Carbon instance
            if (!($validFrom instanceof \Carbon\Carbon)) {
                $validFrom = \Carbon\Carbon::parse($validFrom);
            }
            // Current time must be greater than or equal to valid_from
            if ($now->lt($validFrom)) {
                return false;
            }
        }

        // Check if voucher has expired (valid_until)
        // If valid_until is set, current time must be <= valid_until
        if ($this->valid_until !== null) {
            $validUntil = $this->valid_until;
            // Ensure we have a Carbon instance
            if (!($validUntil instanceof \Carbon\Carbon)) {
                $validUntil = \Carbon\Carbon::parse($validUntil);
            }
            // Current time must be less than or equal to valid_until
            if ($now->gt($validUntil)) {
                return false;
            }
        }

        // Check if usage limit has been reached
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Get the status reason if voucher is not valid
     */
    public function getStatusReason(): ?string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        $now = now();
        
        // Check if voucher has started (valid_from)
        if ($this->valid_from !== null) {
            if ($now->isBefore($this->valid_from)) {
                return 'Not Yet Valid';
            }
        }

        // Check if voucher has expired (valid_until)
        if ($this->valid_until !== null) {
            if ($now->isAfter($this->valid_until)) {
                return 'Expired';
            }
        }

        // Check if usage limit has been reached
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return 'Usage Limit Reached';
        }

        return null; // Valid
    }

    /**
     * Register media collections for voucher images
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('image');
        return $media ? $media->getUrl() : null;
    }
}
