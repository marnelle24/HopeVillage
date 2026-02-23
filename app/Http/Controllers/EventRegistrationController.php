<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
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
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
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

                // Find or create event registration
                $registration = EventRegistration::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                    ],
                    [
                        'type' => 'external_scanner',
                        'status' => 'attended',
                        'registered_at' => now(),
                        'attended_at' => now(),
                    ]
                );

                // Update registration to attended if not already
                if ($registration->status !== 'attended') {
                    $registration->update([
                        'status' => 'attended',
                        'attended_at' => now(),
                        'type' => 'external_scanner',
                    ]);
                } else {
                    // If already attended, ensure type is external_scanner
                    if ($registration->type !== 'external_scanner') {
                        $registration->update([
                            'type' => 'external_scanner',
                        ]);
                    }
                }

                Log::info('Event registration scanned via external scanner', [
                    'member_fin' => $user->fin,
                    'qr_code' => $user->qr_code,
                    'event_code' => $event->event_code,
                    'event_id' => $event->id,
                    'registration_id' => $registration->id,
                    'device_info' => $request->header('User-Agent'),
                    'ip_address' => $request->ip(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Member attendance recorded successfully',
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
                        ],
                    ],
                ], 201);
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
}
