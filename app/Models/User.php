<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'whatsapp_number',
        'user_type',
        'fin',
        'age',
        'gender',
        'qr_code',
        'is_verified',
        'total_points',
        'current_merchant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if user is member
     */
    public function isMember(): bool
    {
        return $this->user_type === 'member';
    }

    /**
     * Check if user is merchant user
     */
    public function isMerchantUser(): bool
    {
        return $this->user_type === 'merchant_user';
    }

    // Relationships
    public function merchants(): BelongsToMany
    {
        return $this->belongsToMany(Merchant::class, 'merchant_user')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class)
            ->withPivot(['status', 'claimed_at', 'redeemed_at'])
            ->withTimestamps();
    }

    public function defaultMerchant()
    {
        return $this->merchants()->wherePivot('is_default', true)->first();
    }

    public function currentMerchant()
    {
        // Check session first (for immediate updates)
        $sessionMerchantId = session('current_merchant_id');
        if ($sessionMerchantId && $this->merchants()->where('merchants.id', $sessionMerchantId)->exists()) {
            return $this->merchants()->where('merchants.id', $sessionMerchantId)->first();
        }

        // Fall back to database value
        if ($this->current_merchant_id) {
            $merchant = $this->merchants()->where('merchants.id', $this->current_merchant_id)->first();
            if ($merchant) {
                return $merchant;
            }
        }

        // Fall back to default merchant
        $default = $this->defaultMerchant();
        if ($default) {
            // Update current_merchant_id to default if not set
            if (!$this->current_merchant_id) {
                $this->update(['current_merchant_id' => $default->id]);
            }
            return $default;
        }

        // Last resort: first merchant
        $first = $this->merchants()->first();
        if ($first && !$this->current_merchant_id) {
            $this->update(['current_merchant_id' => $first->id]);
        }
        return $first;
    }

    public function setCurrentMerchant($merchantId)
    {
        // Verify user has access to this merchant
        if ($this->merchants()->where('merchants.id', $merchantId)->exists()) {
            $this->update(['current_merchant_id' => $merchantId]);
            session(['current_merchant_id' => $merchantId]);
            return true;
        }
        return false;
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

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function createdPrograms(): HasMany
    {
        return $this->hasMany(Program::class, 'created_by');
    }
}
