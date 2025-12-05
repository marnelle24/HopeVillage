<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_activity_id',
        'point_system_config_id',
        'activity_type_id',
        'location_id',
        'amenity_id',
        'points',
        'description',
        'awarded_at',
    ];

    protected function casts(): array
    {
        return [
            'awarded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function memberActivity(): BelongsTo
    {
        return $this->belongsTo(MemberActivity::class);
    }

    public function pointSystemConfig(): BelongsTo
    {
        return $this->belongsTo(PointSystemConfig::class);
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class);
    }
}
