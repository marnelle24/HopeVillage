<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Setting;
use App\Services\GeoService;
use Livewire\Component;
use App\Models\ActivityType;
use App\Models\MemberActivity;
use App\Services\PointsService;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventQrCodeModal extends Component
{
    public $eventCode;
    public $open = false;
    public $event = null;
    public $memberFin = null;
    public $processing = false;
    public $error = null;
    public $success = false;
    public $successMessage = null;
    public $waitingForGeolocation = false;

    protected $listeners = [
        'openEventQrModal' => 'open',
        'closeEventQrModal' => 'close',
    ];

    public function mount($eventCode = null)
    {
        $this->eventCode = $eventCode;
        if ($this->eventCode) {
            $this->loadEvent();
        }
    }

    public function open($eventCode = null, $userLat = null, $userLng = null)
    {
        if ($eventCode) {
            $this->eventCode = $eventCode;
        }
        $this->open = true;
        $this->error = null;
        $this->success = false;
        $this->processing = false;
        $this->waitingForGeolocation = false;
        $this->event = null;
        $this->memberFin = auth()->user()?->fin;

        if ($this->eventCode) {
            $this->loadEvent($userLat, $userLng);
        }
    }

    public function close()
    {
        $this->open = false;
        $this->error = null;
        $this->success = false;
        $this->processing = false;
    }

    public function loadEvent($userLat = null, $userLng = null)
    {
        if ($this->eventCode) {
            $normalizedCode = strtoupper(trim($this->eventCode));
            $this->event = Event::with('location')->where('event_code', $normalizedCode)->first();

            if (! $this->event) {
                $this->error = 'Event not found.';
                return;
            }

            if ($userLat !== null && $userLng !== null) {
                $this->processEventAttendance($userLat, $userLng);
            } else {
                $this->waitingForGeolocation = true;
                $this->dispatch('event-modal-needs-geolocation');
            }
        }
    }

    public function processEventAttendance($latitude = null, $longitude = null)
    {
        $user = auth()->user();
        
        if (!$user || !$user->fin) {
            $this->error = 'Please login to scan QR codes.';
            return;
        }

        if (!$this->event) {
            $this->error = 'Event not found.';
            return;
        }

        // Check if user is a member
        if ($user->user_type !== 'member') {
            $this->error = 'User is not a member.';
            return;
        }

        // Validate geolocation proximity before check-in (event's location)
        if ($this->event->location) {
            $geoService = app(GeoService::class);
            $validation = $geoService->validateProximity($latitude, $longitude, $this->event->location);

            if (! $validation['valid']) {
                $this->error = $validation['message'];
                $this->dispatch('show-proximity-alert', message: $validation['message']);

                return;
            }
        }

        $this->processing = true;
        $this->waitingForGeolocation = false;
        $this->error = null;
        $this->success = false;
        $this->successMessage = null;

        try 
        {
            // After granting the points for attend event. 
            // Check if member has a recent entry at this location within the time gap in the settings
            $timeGapSeconds = (int) Setting::get('entry_time_gap', 3600);
            $timeGapAgo = now()->subSeconds($timeGapSeconds);
            
            $recentEntry = MemberActivity::where('user_id', $user->id)
                ->where('location_id', $this->event->location_id)
                ->whereHas('activityType', function($query) {
                    $query->where('name', 'ENTRY');
                })
                ->where('activity_time', '>=', $timeGapAgo)
                ->exists();
            
            if (!$recentEntry) 
            {
                $activityType = ActivityType::where('name', 'ENTRY')->first();
                if ($activityType) {
                    // Create member activity record
                    $memberActivity = MemberActivity::create([
                        'user_id' => $user->id,
                        'activity_type_id' => $activityType->id,
                        'location_id' => $this->event->location_id,
                        'amenity_id' => null,
                        'activity_time' => now(),
                        'description' => "Member Re-ENTRY to event {$this->event->title}",
                        'metadata' => [
                            'scanned_at' => now()->toIso8601String(),
                            'qr_code' => $user->qr_code,
                            'event_code' => $this->event->event_code,
                            'device_info' => request()->header('User-Agent'),
                            'access_type' => 'built_in_scanner',
                            'ip_address' => request()->ip(),
                        ],
                    ]);
                    
                    // Award points for RE-ENTRY activity
                    app(PointsService::class)->award(
                        user: $user,
                        activityName: PointsService::ACTIVITY_LOCATION_ENTRY,
                        description: "Member Re-ENTRY to event {$this->event->title} - entry_time_gap lapses",
                        locationId: $this->event->location_id,
                        memberActivityId: $memberActivity->id,
                    );
                }
            }


            DB::transaction(function () use ($user) 
            {
                // Check if member has already attended this event
                $existingRegistration = EventRegistration::where('user_id', $user->id)
                    ->where('event_id', $this->event->id)
                    ->where('status', 'attended')
                    ->first();
                
                if ($existingRegistration) {
                    $this->error = 'You have already attended this event.';
                    $this->processing = false;
                    return;
                }

                // Find or create event registration
                $registration = EventRegistration::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'event_id' => $this->event->id,
                    ],
                    [
                        'type' => 'event_qr_code',
                        'status' => 'registered',
                        'registered_at' => now(),
                    ]
                );

                // Update registration to attended if not already
                if ($registration->status !== 'attended') {
                    $registration->update([
                        'status' => 'attended',
                        'attended_at' => now(),
                    ]);
                }

                // Find ATTEND activity type (do not create)
                $activityType = ActivityType::where('name', 'member_attend_event')->first();
                if (! $activityType) {
                    $this->error = 'Member attend event activity type is not configured. Please contact an administrator.';
                    $this->processing = false;
                    return;
                }

                // Create member activity record
                $memberActivity = MemberActivity::create([
                    'user_id' => $user->id,
                    'activity_type_id' => $activityType->id,
                    'location_id' => $this->event->location_id,
                    'amenity_id' => null,
                    'activity_time' => now(),
                    'description' => "Member attend event at {$this->event->title}",
                    'metadata' => [
                        'scanned_at' => now()->toIso8601String(),
                        'event_code' => $this->event->event_code,
                        'event_id' => $this->event->id,
                        'qr_code' => $user->qr_code,
                        'device_info' => request()->header('User-Agent'),
                        'access_type' => 'built_in_scanner',
                        'ip_address' => request()->ip(),
                    ],
                ]);

                // Award points for ATTEND activity
                app(PointsService::class)->award(
                    user: $user,
                    activityName: PointsService::ACTIVITY_EVENT_ATTEND,
                    description: 'Member attended event',
                    locationId: $this->event->location_id,
                    memberActivityId: $memberActivity->id,
                );

                // Award points for ENTRY activity
                $pointsBefore = $user->total_points;
                $pointsAwarded = 0;
                
                $user->refresh();
                $pointsAwarded = $user->total_points - $pointsBefore;

                Log::info('Member event attendance recorded', [
                    'member_fin' => $user->fin,
                    'event_code' => $this->event->event_code,
                    'event_id' => $this->event->id,
                    'activity_type' => 'ATTEND',
                    'points_awarded' => $pointsAwarded,
                ]);

                // Set success message
                $this->success = true;
                $this->successMessage = "SUCCESS";
                
                // Dispatch event to update points header in real-time
                $this->dispatch('points-updated');
                
                session()->flash('event-attend-success', 'SUCCESS');
                $this->processing = false;
            });
        } catch (\Exception $e) {
            Log::error('Failed to record event attendance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->error = 'Failed to process event attendance: ' . ($e->getMessage() ?? 'Unknown error');
            $this->processing = false;
        }
    }

    public function processScan()
    {
        // This method is kept for backward compatibility but now calls processEventAttendance
        $this->processEventAttendance();
    }

    public function render()
    {
        return view('livewire.event-qr-code-modal');
    }
}
