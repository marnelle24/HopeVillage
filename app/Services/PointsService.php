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
    public const ACTIVITY_ACCOUNT_VERIFICATION = 'account_verification';
    public const ACTIVITY_LOCATION_ENTRY = 'member_entry_location';
    public const ACTIVITY_EVENT_JOIN = 'member_join_event';
    public const ACTIVITY_EVENT_ATTEND = 'member_attend_event';
    public const ACTIVITY_VOUCHER_CLAIM = 'member_claim_voucher';
    public const ACTIVITY_VOUCHER_REDEEM = 'member_redeem_voucher';

    // Fallback points (used only if no admin configuration exists)
    private const FALLBACK_POINTS_ACCOUNT_VERIFICATION = 10;
    private const FALLBACK_POINTS_LOCATION_ENTRY = 10;
    private const FALLBACK_POINTS_EVENT_JOIN = 10;
    private const FALLBACK_POINTS_EVENT_ATTEND = 20;
    private const FALLBACK_POINTS_VOUCHER_CLAIM = 10;
    private const FALLBACK_POINTS_VOUCHER_REDEEM = 5;

    public function awardAccountVerification(User $user): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_ACCOUNT_VERIFICATION,
            description: 'Account created and verified',
            locationId: null,
        );
    }

    public function awardLocationEntry(User $user, int $locationId): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_LOCATION_ENTRY,
            description: 'Member entry to location',
            locationId: $locationId,
        );
    }

    public function awardEventJoin(User $user, Event $event): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_EVENT_JOIN,
            description: 'Member joined event',
            locationId: $event->location_id,
        );
    }

    public function awardEventAttend(User $user, Event $event): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_EVENT_ATTEND,
            description: 'Member attended event',
            locationId: $event->location_id,
        );
    }

    public function awardVoucherClaim(User $user, Voucher $voucher): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_VOUCHER_CLAIM,
            description: 'Member claimed voucher ' . $voucher->voucher_code,
            locationId: null,
        );
    }

    public function awardVoucherRedeem(User $user, Voucher $voucher): void
    {
        $this->award(
            user: $user,
            activityName: self::ACTIVITY_VOUCHER_REDEEM,
            description: 'Member redeemed voucher ' . $voucher->voucher_code,
            locationId: null,
        );
    }

    /**
     * Get fallback points based on activity name
     */
    private function getFallbackPoints(string $activityName): int
    {
        return match ($activityName) {
            self::ACTIVITY_ACCOUNT_VERIFICATION => self::FALLBACK_POINTS_ACCOUNT_VERIFICATION,
            self::ACTIVITY_LOCATION_ENTRY => self::FALLBACK_POINTS_LOCATION_ENTRY,
            self::ACTIVITY_EVENT_JOIN => self::FALLBACK_POINTS_EVENT_JOIN,
            self::ACTIVITY_EVENT_ATTEND => self::FALLBACK_POINTS_EVENT_ATTEND,
            self::ACTIVITY_VOUCHER_CLAIM => self::FALLBACK_POINTS_VOUCHER_CLAIM,
            self::ACTIVITY_VOUCHER_REDEEM => self::FALLBACK_POINTS_VOUCHER_REDEEM,
            default => 0,
        };
    }

    /**
     * Get or create point system configuration
     */
    private function getOrCreateConfig(
        int $activityTypeId,
        ?int $locationId = null,
        ?int $amenityId = null,
        string $activityName = '',
        ?string $description = null
    ): PointSystemConfig {
        // First, try to find a specific config (with location and/or amenity)
        $config = PointSystemConfig::where('activity_type_id', $activityTypeId)
            ->where('location_id', $locationId)
            ->where('amenity_id', $amenityId)
            ->where('is_active', true)
            ->first();

        if ($config) {
            return $config;
        }

        // If no specific config, try global config (no location, no amenity)
        $globalConfig = PointSystemConfig::where('activity_type_id', $activityTypeId)
            ->whereNull('location_id')
            ->whereNull('amenity_id')
            ->where('is_active', true)
            ->first();

        if ($globalConfig) {
            return $globalConfig;
        }

        // No admin config found, use fallback and create a default config
        $points = $this->getFallbackPoints($activityName);
        
        return PointSystemConfig::query()->firstOrCreate(
            [
                'activity_type_id' => $activityTypeId,
                'location_id' => $locationId,
                'amenity_id' => $amenityId,
            ],
            [
                'points' => $points,
                'description' => $description ?? $activityName,
                'is_active' => true,
            ]
        );
    }

    public function award(
        User $user,
        string $activityName,
        ?string $description = null,
        ?int $locationId = null,
        ?int $memberActivityId = null,
        ?int $amenityId = null,
    ): void {
        DB::transaction(function () use ($user, $activityName, $description, $locationId, $memberActivityId, $amenityId) {
            $activityType = ActivityType::query()->firstOrCreate(
                ['name' => $activityName],
                ['description' => $activityName, 'is_active' => true]
            );

            // Get or create the point system configuration
            $config = $this->getOrCreateConfig(
                $activityType->id,
                $locationId,
                $amenityId,
                $activityName,
                $description
            );

            // Use points from the configuration
            $points = $config->points;

            PointLog::query()->create([
                'user_id' => $user->id,
                'member_activity_id' => $memberActivityId,
                'point_system_config_id' => $config->id,
                'activity_type_id' => $activityType->id,
                'location_id' => $locationId,
                'amenity_id' => $amenityId,
                'points' => $points,
                'description' => $description ?? $activityName,
                'awarded_at' => now(),
            ]);

            // Keep user's running total in sync.
            $user->increment('total_points', $points);
        });
    }
}


