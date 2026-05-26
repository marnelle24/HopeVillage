<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ActivityType;
use App\Models\MemberActivity;
use App\Models\Setting;
use App\Models\User;
use App\Services\PointsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventRegistrationController extends Controller
{
    /**
     * Handle event QR code scan to register member attendance
     *
     * Expects:
     * - event_code: Event's unique code (scanned from QR code)
     * - qr_code: Member's QR code (scanned from member QR code)
     * - source (optional): `admin` when called from the admin event profile
     *   page's "Scan Member QR Code" modal. Any other value (or omitted)
     *   is treated as a public external scanner device.
     *
     * The registration `type` is derived from `source`:
     *   admin             -> user_qr_code
     *   external scanner  -> external_scanner
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function scan(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'event_code' => ['required', 'string', 'max:255'],
            'qr_code' => ['required', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'in:admin,external_scanner'],
        ]);

        // Only scans initiated from the admin event profile page record the
        // registration as `user_qr_code`; everything else stays
        // `external_scanner` to preserve the existing public scanner contract.
        $registrationType = ($validated['source'] ?? null) === 'admin'
            ? 'user_qr_code'
            : 'external_scanner';

        try {
            return DB::transaction(function () use ($validated, $request, $registrationType) {
                $pointsBefore = null;
                $pointsAwarded = 0;

                // Normalize event code (trim and uppercase)
                $normalizedEventCode = strtoupper(trim($validated['event_code']));
                
                // Find event by event_code
                $event = Event::where('event_code', $normalizedEventCode)->first();
                
                if (!$event) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Event not found',
                        'error' => "No event found with code: {$validated['event_code']}",
                    ], 404);
                }

                // Check if event is active
                if ($event->status !== 'published') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Event is not active',
                        'error' => "Event '{$event->title}' (code: {$validated['event_code']}) is not active",
                    ], 422);
                }

                // Block past/finished events - no points or activities for expired events
                $eventEnd = $event->end_date ?? $event->start_date;
                if ($eventEnd && $eventEnd->isPast()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Event has ended',
                        'error' => 'This event has ended. QR code scanning is no longer available for past events.',
                    ], 422);
                }

                // Find member by QR code
                $user = User::where('qr_code', $validated['qr_code'])->first();
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Member not found',
                        'error' => "No member found with QR code: {$validated['qr_code']}",
                    ], 404);
                }

                // Check if user is a member
                if ($user->user_type !== 'member') {
                    return response()->json([
                        'success' => false,
                        'message' => 'User is not a member',
                        'error' => "User with QR code {$validated['qr_code']} is not a member (user_type: {$user->user_type})",
                    ], 422);
                }

                $pointsBefore = (int) $user->total_points;

                // Find or create event registration
                $registration = EventRegistration::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                    ],
                    [
                        'type' => $registrationType,
                        'status' => 'attended',
                        'registered_at' => now(),
                        'attended_at' => now(),
                    ]
                );

                // The admin-facing "already attended" message must reflect the
                // registration row, not just the member-activity ledger.
                //  - Newly-created registration  -> just attended (success)
                //  - Existing but not yet attended -> promote and treat as success
                //  - Existing and already attended -> true duplicate scan
                $attendedJustNow = $registration->wasRecentlyCreated;

                if (! $registration->wasRecentlyCreated) {
                    if ($registration->status !== 'attended') {
                        $registration->update([
                            'status' => 'attended',
                            'attended_at' => now(),
                            'type' => $registrationType,
                        ]);
                        $attendedJustNow = true;
                    } elseif ($registration->type !== $registrationType) {
                        // Already attended via another channel; just tag the scanner type.
                        $registration->update([
                            'type' => $registrationType,
                        ]);
                    }
                }

                $attendActivityType = ActivityType::where('name', PointsService::ACTIVITY_EVENT_ATTEND)->first();
                if (! $attendActivityType) {
                    throw new \RuntimeException('Member attend event activity type is not configured.');
                }

                $memberActivity = MemberActivity::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'activity_type_id' => $attendActivityType->id,
                        'event_id' => $event->id,
                    ],
                    [
                        'location_id' => $event->location_id,
                        'amenity_id' => null,
                        'activity_time' => now(),
                        'description' => "Member attend event at {$event->title}",
                        'metadata' => [
                            'scanned_at' => now()->toIso8601String(),
                            'event_code' => $event->event_code,
                            'event_id' => $event->id,
                            'qr_code' => $user->qr_code,
                            'device_info' => $request->header('User-Agent'),
                            'access_type' => $registrationType,
                            'ip_address' => $request->ip(),
                        ],
                    ]
                );

                if ($memberActivity->wasRecentlyCreated) {
                    app(PointsService::class)->award(
                        user: $user,
                        activityName: PointsService::ACTIVITY_EVENT_ATTEND,
                        description: 'Member attended event',
                        locationId: $event->location_id,
                        memberActivityId: $memberActivity->id,
                    );

                    // Mirror the member self-scan behaviour: award an additional
                    // location ENTRY point if the member has no recent ENTRY at
                    // this location within the configured entry_time_gap window.
                    $this->maybeAwardEntryBonus($user, $event, $request, $registrationType);
                }

                $activityAlreadyLogged = ! $memberActivity->wasRecentlyCreated;
                // "Already attended" is driven by the registration row, so a
                // freshly-created or just-promoted registration always reports
                // success even if a stale member-activity row existed.
                $alreadyAttended = ! $attendedJustNow;

                // Refresh and compute how many points (if any) were credited
                // by this scan so the UI can surface it to the admin.
                $user->refresh();
                $pointsAwarded = max(0, ((int) $user->total_points) - ($pointsBefore ?? (int) $user->total_points));

                Log::info('Event registration scanned', [
                    'registration_type' => $registrationType,
                    'source' => $validated['source'] ?? 'external_scanner',
                    'member_fin' => $user->fin,
                    'qr_code' => $user->qr_code,
                    'event_code' => $event->event_code,
                    'event_id' => $event->id,
                    'registration_id' => $registration->id,
                    'registration_was_new' => $registration->wasRecentlyCreated,
                    'attended_just_now' => $attendedJustNow,
                    'member_activity_id' => $memberActivity->id,
                    'activity_already_logged' => $activityAlreadyLogged,
                    'points_awarded' => $pointsAwarded,
                    'device_info' => $request->header('User-Agent'),
                    'ip_address' => $request->ip(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $alreadyAttended
                        ? 'Member attendance was already recorded for this event'
                        : 'Member attendance recorded successfully',
                    'data' => [
                        'member' => [
                            'fin' => $user->fin,
                            'qr_code' => $user->qr_code,
                            'name' => $user->name,
                        ],
                        'event' => [
                            'id' => $event->id,
                            'code' => $event->event_code,
                            'title' => $event->title,
                        ],
                        'registration' => [
                            'id' => $registration->id,
                            'status' => $registration->status,
                            'type' => $registration->type,
                            'attended_at' => $registration->attended_at->toIso8601String(),
                            'was_new' => $registration->wasRecentlyCreated,
                            'attended_just_now' => $attendedJustNow,
                        ],
                        'activity' => [
                            'id' => $memberActivity->id,
                            // Kept for backwards compatibility / debugging.
                            'already_logged' => $activityAlreadyLogged,
                        ],
                        'already_attended' => $alreadyAttended,
                        'points_awarded' => $pointsAwarded,
                    ],
                ], $alreadyAttended ? 200 : 201);
            });
        } catch (\Exception $e) {
            Log::error('Failed to record event registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to record attendance',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Award an extra ENTRY point at the event's location when the member has
     * no recent ENTRY activity there within the configured entry_time_gap.
     *
     * Mirrors the bonus logic in {@see \App\Livewire\EventQrCodeModal::processEventAttendance}
     * so admin scan (user_qr_code) and member self-scan / external-scanner flows
     * stay consistent. The caller passes its `$registrationType` through so the
     * resulting activity metadata reflects the actual scan source.
     */
    private function maybeAwardEntryBonus(User $user, Event $event, Request $request, string $registrationType): void
    {
        if (! $event->location_id) {
            return;
        }

        $timeGapSeconds = (int) Setting::get('entry_time_gap', 3600);
        $timeGapAgo = now()->subSeconds($timeGapSeconds);

        $recentEntry = MemberActivity::where('user_id', $user->id)
            ->where('location_id', $event->location_id)
            ->whereHas('activityType', function ($query) {
                $query->where('name', 'ENTRY');
            })
            ->where('activity_time', '>=', $timeGapAgo)
            ->exists();

        if ($recentEntry) {
            return;
        }

        $entryActivityType = ActivityType::where('name', 'ENTRY')->first();
        if (! $entryActivityType) {
            return;
        }

        $entryActivity = MemberActivity::create([
            'user_id' => $user->id,
            'activity_type_id' => $entryActivityType->id,
            'location_id' => $event->location_id,
            'amenity_id' => null,
            'activity_time' => now(),
            'description' => "Member Re-ENTRY to event {$event->title}",
            'metadata' => [
                'scanned_at' => now()->toIso8601String(),
                'qr_code' => $user->qr_code,
                'event_code' => $event->event_code,
                'event_id' => $event->id,
                'device_info' => $request->header('User-Agent'),
                'access_type' => $registrationType,
                'ip_address' => $request->ip(),
            ],
        ]);

        app(PointsService::class)->award(
            user: $user,
            activityName: PointsService::ACTIVITY_LOCATION_ENTRY,
            description: "Member Re-ENTRY to event {$event->title} - entry_time_gap lapses",
            locationId: $event->location_id,
            memberActivityId: $entryActivity->id,
        );
    }
}
