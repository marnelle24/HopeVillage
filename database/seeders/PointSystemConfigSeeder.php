<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use App\Models\PointSystemConfig;
use App\Services\PointsService;
use Illuminate\Database\Seeder;

class PointSystemConfigSeeder extends Seeder
{
    /**
     * Seed default activity types and point system configurations.
     * 
     * Note: Points values must match PointsService::FALLBACK_POINTS_* constants:
     * - ACTIVITY_ACCOUNT_VERIFICATION: 10
     * - ACTIVITY_LOCATION_ENTRY: 10
     * - ACTIVITY_EVENT_JOIN: 10
     * - ACTIVITY_EVENT_ATTEND: 20
     * - ACTIVITY_VOUCHER_CLAIM: 10
     * - ACTIVITY_VOUCHER_REDEEM: 5
     */
    public function run(): void
    {
        $defaultConfigs = [
            [
                'activity_name' => PointsService::ACTIVITY_ACCOUNT_VERIFICATION,
                'activity_description' => 'Account created and verified',
                'points' => 10, // Must match PointsService::FALLBACK_POINTS_ACCOUNT_VERIFICATION
                'description' => 'Account created and verified',
            ],
            [
                'activity_name' => PointsService::ACTIVITY_LOCATION_ENTRY,
                'activity_description' => 'Member entry to location',
                'points' => 10, // Must match PointsService::FALLBACK_POINTS_LOCATION_ENTRY
                'description' => 'Member entry to location',
            ],
            [
                'activity_name' => PointsService::ACTIVITY_EVENT_JOIN,
                'activity_description' => 'Member joined event',
                'points' => 10, // Must match PointsService::FALLBACK_POINTS_EVENT_JOIN
                'description' => 'Member joined event',
            ],
            [
                'activity_name' => PointsService::ACTIVITY_EVENT_ATTEND,
                'activity_description' => 'Member attended event',
                'points' => 20, // Must match PointsService::FALLBACK_POINTS_EVENT_ATTEND
                'description' => 'Member attended event',
            ],
            [
                'activity_name' => PointsService::ACTIVITY_VOUCHER_CLAIM,
                'activity_description' => 'Member claimed voucher',
                'points' => 10, // Must match PointsService::FALLBACK_POINTS_VOUCHER_CLAIM
                'description' => 'Member claimed voucher',
            ],
            [
                'activity_name' => PointsService::ACTIVITY_VOUCHER_REDEEM,
                'activity_description' => 'Member redeemed voucher',
                'points' => 5, // Must match PointsService::FALLBACK_POINTS_VOUCHER_REDEEM
                'description' => 'Member redeemed voucher',
            ],
        ];

        foreach ($defaultConfigs as $config) {
            // Create or get activity type
            $activityType = ActivityType::firstOrCreate(
                ['name' => $config['activity_name']],
                [
                    'description' => $config['activity_description'],
                    'is_active' => true,
                ]
            );

            // Create default point system configuration (global - no location or amenity)
            PointSystemConfig::firstOrCreate(
                [
                    'activity_type_id' => $activityType->id,
                    'location_id' => null,
                    'amenity_id' => null,
                ],
                [
                    'points' => $config['points'],
                    'description' => $config['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}

