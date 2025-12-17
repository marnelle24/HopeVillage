<?php

namespace App\Services;

use App\Models\ActivityType;
use App\Models\Event;
use App\Models\PointLog;
use App\Models\PointSystemConfig;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class PointsService
{
    public const ACTIVITY_LOCATION_ENTRY = 'member_entry_location';
    public const ACTIVITY_EVENT_JOIN = 'member_join_event';
    public const ACTIVITY_EVENT_ATTEND = 'member_attend_event';
    public const ACTIVITY_VOUCHER_CLAIM = 'member_claim_voucher';
    public const ACTIVITY_VOUCHER_REDEEM = 'member_redeem_voucher';

    public const POINTS_LOCATION_ENTRY = 10;
    public const POINTS_EVENT_JOIN = 10;
    public const POINTS_EVENT_ATTEND = 20;
    public const POINTS_VOUCHER_CLAIM = 10;
    public const POINTS_VOUCHER_REDEEM = 5;

    public function awardLocationEntry(User $user, int $locationId): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_LOCATION_ENTRY,
            points: self::POINTS_LOCATION_ENTRY,
            description: 'Member entry to location',
            locationId: $locationId,
        );
    }

    public function awardEventJoin(User $user, Event $event): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_EVENT_JOIN,
            points: self::POINTS_EVENT_JOIN,
            description: 'Member joined event',
            locationId: $event->location_id,
        );
    }

    public function awardEventAttend(User $user, Event $event): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_EVENT_ATTEND,
            points: self::POINTS_EVENT_ATTEND,
            description: 'Member attended event',
            locationId: $event->location_id,
        );
    }

    public function awardVoucherClaim(User $user, Voucher $voucher): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_VOUCHER_CLAIM,
            points: self::POINTS_VOUCHER_CLAIM,
            description: 'Member claimed voucher ' . $voucher->voucher_code,
            locationId: null,
        );
    }

    public function awardVoucherRedeem(User $user, Voucher $voucher): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_VOUCHER_REDEEM,
            points: self::POINTS_VOUCHER_REDEEM,
            description: 'Member redeemed voucher ' . $voucher->voucher_code,
            locationId: null,
        );
    }

    public function award(
        User $user,
        string $activityName,
        int $points,
        ?string $description = null,
        ?int $locationId = null,
        ?int $memberActivityId = null,
        ?int $amenityId = null,
    ): void {
        DB::transaction(function () use ($user, $activityName, $points, $description, $locationId, $memberActivityId, $amenityId) {
            $activityType = ActivityType::query()->firstOrCreate(
                ['name' => $activityName],
                ['description' => $activityName, 'is_active' => true]
            );

            $config = PointSystemConfig::query()->firstOrCreate(
                [
                    'activity_type_id' => $activityType->id,
                    'location_id' => $locationId,
                    'amenity_id' => $amenityId,
                ],
                [
                    'points' => $points,
                    'description' => $description,
                    'is_active' => true,
                ]
            );

            PointLog::query()->create([
                'user_id' => $user->id,
                'member_activity_id' => $memberActivityId,
                'point_system_config_id' => $config->id,
                'activity_type_id' => $activityType->id,
                'location_id' => $locationId,
                'amenity_id' => $amenityId,
                'points' => $points,
                'description' => $description,
                'awarded_at' => now(),
            ]);

            // Keep user's running total in sync.
            $user->increment('total_points', $points);
        });
    }
}


