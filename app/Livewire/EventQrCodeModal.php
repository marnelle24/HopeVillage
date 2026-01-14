<?php

namespace App\Livewire;

use App\Models\ActivityType;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\MemberActivity;
use App\Services\PointsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

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

    public function open($eventCode = null)
    {
        if ($eventCode) {
            $this->eventCode = $eventCode;
        }
        $this->open = true;
        $this->error = null;
        $this->success = false;
        $this->processing = false;
        $this->event = null; // Reset event
        $this->memberFin = auth()->user()?->fin;
        
        if ($this->eventCode) {
            $this->loadEvent();
        }
    }

    public function close()
    {
        $this->open = false;
        $this->error = null;
        $this->success = false;
        $this->processing = false;
    }

    public function loadEvent()
    {
        if ($this->eventCode) {
            // Normalize the event code (trim and uppercase)
            $normalizedCode = strtoupper(trim($this->eventCode));
            
            // Event code already includes EVT- prefix in database
            $this->event = Event::where('event_code', $normalizedCode)->first();
            
            if (!$this->event) {
                $this->error = 'Event not found.';
                return;
            }
            
            // Automatically process event attendance
            $this->processEventAttendance();
        }
    }
    
    public function processEventAttendance()
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

        $this->processing = true;
        $this->error = null;
        $this->success = false;
        $this->successMessage = null;

        try {
            DB::transaction(function () use ($user) {
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

                // Find or create activity type
                $activityType = ActivityType::firstOrCreate(
                    ['name' => 'ATTEND'],
                    [
                        'description' => 'Attend activity',
                        'is_active' => true,
                    ]
                );

                // Create member activity record
                $memberActivity = MemberActivity::create([
                    'user_id' => $user->id,
                    'activity_type_id' => $activityType->id,
                    'location_id' => $this->event->location_id,
                    'amenity_id' => null,
                    'activity_time' => now(),
                    'description' => "Member ATTEND at {$this->event->title}",
                    'metadata' => [
                        'scanned_at' => now()->toIso8601String(),
                        'event_code' => $this->event->event_code,
                        'event_id' => $this->event->id,
                        'member_fin' => $user->fin,
                        'device_info' => request()->header('User-Agent'),
                        'ip_address' => request()->ip(),
                    ],
                ]);

                // Award points for ATTEND activity
                $pointsBefore = $user->total_points;
                $pointsAwarded = 0;
                
                app(PointsService::class)->award(
                    user: $user,
                    activityName: PointsService::ACTIVITY_EVENT_ATTEND,
                    description: 'Member attended event',
                    locationId: $this->event->location_id,
                    memberActivityId: $memberActivity->id,
                );
                
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
