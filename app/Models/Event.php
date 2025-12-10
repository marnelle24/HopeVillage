<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'created_by',
        'title',
        'description',
        'start_date',
        'end_date',
        'venue',
        'max_participants',
        'status',
        'event_code',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('type', 'event');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->event_code)) {
                $event->event_code = static::generateUniqueEventCode();
            }
        });
    }

    /**
     * Generate a unique event code for QR code generation.
     *
     * @return string
     */
    protected static function generateUniqueEventCode(): string
    {
        do {
            $code = 'EVT-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (static::where('event_code', $code)->exists());

        return $code;
    }
}
