<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use App\Models\Location;
use App\Models\MemberActivity;
use App\Models\Setting;
use App\Models\User;
use App\Services\PointsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberActivityController extends Controller
{
    /**
     * Handle QR code scan to record member activity
     * 
     * Expects:
     * - member_fin: Member's FIN (scanned from QR code)
     * - location_code: Location's unique code (configured in scanner device)
     * - type_of_activity: Activity type name (e.g., 'ENTRY', 'EXIT', etc.)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function scan(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'qr_code' => ['required', 'string', 'max:255'],
            'member_fin' => ['nullable', 'string', 'exists:users,fin'],
            'location_code' => ['required', 'string', 'exists:locations,location_code'],
            'type_of_activity' => ['required', 'string', 'max:255'],
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                // Find member by FIN - first check if user exists
                $user = User::where('qr_code', $validated['qr_code'])->first();
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Member not found',
                        'error' => "No member found with qr code: {$validated['qr_code']}",
                    ], 404);
                }

                // Check if user is a member
                if ($user->user_type !== 'member') {
                    return response()->json([
                        'success' => false,
                        'message' => 'User is not a member',
                        'error' => "User with qr code {$validated['qr_code']} is not a member (user_type: {$user->user_type})",
                    ], 422);
                }

                $member = $user;

                // Find location by location_code
                $location = Location::where('location_code', $validated['location_code'])->first();
                
                if (!$location) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Location not found',
                        'error' => "No location found with code: {$validated['location_code']}",
                    ], 404);
                }

                // Check if location is active
                if (!$location->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Location is not active',
                        'error' => "Location '{$location->name}' (code: {$validated['location_code']}) is not active",
                    ], 422);
                }

                // Validate entry time gap for ENTRY activities
                if (strtoupper($validated['type_of_activity']) === 'ENTRY') 
                {
                    // Get dynamic time gap from settings (default: 3600 seconds = 1 hour)
                    $timeGapSeconds = (int) Setting::get('entry_time_gap', 3600);
                    $timeGapAgo = now()->subSeconds($timeGapSeconds);
                    
                    // Check if member has a recent entry at this location within the time gap
                    $recentEntry = MemberActivity::where('user_id', $member->id)
                        ->where('location_id', $location->id)
                        ->whereHas('activityType', function($query) {
                            $query->where('name', 'ENTRY');
                        })
                        ->where('activity_time', '>=', $timeGapAgo)
                        ->exists();
                    
                    if ($recentEntry) {
                        return response()->json([
                            'success' => false,
                            'message' => 'it requires a ' . $timeGapSeconds . ' second(s) gap to scan again.',
                            'error' => "Member has a recent entry at this location within the time gap",
                        ], 422);
                    }
                }

                // Find or create activity type
                $activityType = ActivityType::firstOrCreate(
                    ['name' => $validated['type_of_activity']],
                    [
                        'description' => ucfirst(strtolower($validated['type_of_activity'])) . ' activity',
                        'is_active' => true,
                    ]
                );

                // Create member activity record
                $memberActivity = MemberActivity::create([
                    'user_id' => $member->id,
                    'activity_type_id' => $activityType->id,
                    'location_id' => $location->id,
                    'amenity_id' => null,
                    'activity_time' => now(),
                    'description' => "Member {$validated['type_of_activity']} at {$location->name}",
                    'metadata' => [
                        'scanned_at' => now()->toIso8601String(),
                        'location_code' => $location->location_code,
                        'member_fin' => $member->fin,
                        'qr_code' => $member->qr_code,
                        'device_info' => $request->header('User-Agent'),
                        'ip_address' => $request->ip(),
                    ],
                ]);

                // Award points if this is an ENTRY activity
                $pointsBefore = $member->total_points;
                $pointsAwarded = 0;
                if (strtoupper($validated['type_of_activity']) === 'ENTRY') {
                    app(PointsService::class)->award(
                        user: $member,
                        activityName: PointsService::ACTIVITY_LOCATION_ENTRY,
                        description: 'Member entry to location',
                        locationId: $location->id,
                        memberActivityId: $memberActivity->id,
                    );
                    $member->refresh();
                    $pointsAwarded = $member->total_points - $pointsBefore;
                }

                Log::info('Member activity scanned', [
                    'member_fin' => $member->fin,
                    'location_code' => $location->location_code,
                    'activity_type' => $validated['type_of_activity'],
                    'points_awarded' => $pointsAwarded,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Activity recorded successfully',
                    'data' => [
                        'member' => [
                            'fin' => $member->fin,
                            'name' => $member->name,
                            'total_points' => $member->total_points,
                        ],
                        'location' => [
                            'code' => $location->location_code,
                            'name' => $location->name,
                        ],
                        'activity' => [
                            'id' => $memberActivity->id,
                            'type' => $activityType->name,
                            'activity_time' => $memberActivity->activity_time->toIso8601String(),
                        ],
                        'points_awarded' => $pointsAwarded,
                    ],
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error('Failed to record member activity', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to record activity',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}

