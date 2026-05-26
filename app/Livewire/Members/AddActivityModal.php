<?php

namespace App\Livewire\Members;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\MemberActivity;
use App\Models\PointLog;
use App\Models\PointSystemConfig;
use App\Models\User;
use App\Services\PointsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AddActivityModal extends Component
{
    public User $member;

    public bool $open = false;

    public ?string $pointSystemConfigId = null;

    /** Datetime for the manual activity entry (Y-m-d\TH:i for datetime-local). */
    public ?string $activityDateTime = null;

    /** Selected event when the chosen activity is `member_attend_event`. */
    public ?int $eventId = null;

    public bool $submitting = false;

    public ?string $error = null;

    public ?string $successMessage = null;

    protected $listeners = [
        'openAddActivityModal' => 'open',
    ];

    protected function rules(): array
    {
        $rules = [
            'pointSystemConfigId' => ['required', 'string', 'exists:point_system_configs,id'],
            'activityDateTime' => ['required', 'string', 'date'],
        ];

        if ($this->isAttendEventActivity()) {
            $rules['eventId'] = ['required', 'integer', 'exists:events,id'];
        }

        return $rules;
    }

    public function open(?int $memberId = null): void
    {
        if ($memberId && $memberId !== $this->member->id) {
            $this->member = User::findOrFail($memberId);
        }

        if (! auth()->user()?->isAdmin()) {
            return;
        }

        $this->resetValidation();
        $this->pointSystemConfigId = null;
        $this->eventId = null;
        $this->activityDateTime = now()->format('Y-m-d\TH:i');
        $this->error = null;
        $this->successMessage = null;
        $this->open = true;
    }

    public function updatedPointSystemConfigId($value): void
    {
        // Reset event selection whenever the activity changes so a stale event
        // does not get attached to a non-event activity.
        if (! $this->isAttendEventActivity()) {
            $this->eventId = null;
        }

        $this->resetErrorBag('eventId');
    }

    /**
     * Whether the currently-selected point system config is for the
     * `member_attend_event` activity type. Drives the event dropdown.
     */
    public function isAttendEventActivity(): bool
    {
        if (! $this->pointSystemConfigId) {
            return false;
        }

        $config = $this->pointSystemConfigs->firstWhere('id', (int) $this->pointSystemConfigId);

        return $config?->activityType?->name === PointsService::ACTIVITY_EVENT_ATTEND;
    }

    public function close(): void
    {
        $this->open = false;
        $this->error = null;
        $this->successMessage = null;
        $this->submitting = false;
    }

    public function submit(): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->error = 'You do not have permission to add activities.';
            return;
        }

        $this->validate();

        $this->submitting = true;
        $this->error = null;
        $this->successMessage = null;

        try {
            $config = PointSystemConfig::with(['activityType', 'location'])
                ->where('is_active', true)
                ->findOrFail($this->pointSystemConfigId);

            $isAttendEvent = $config->activityType?->name === PointsService::ACTIVITY_EVENT_ATTEND;
            $event = $isAttendEvent ? Event::findOrFail($this->eventId) : null;

            // The `member_activities_user_activity_event_unique` index on
            // (user_id, activity_type_id, event_id) means a previously-voided
            // attend row still occupies the slot. Look up the existing row so we
            // can either block (if it is live) or revive it (if it is voided).
            $existingAttend = null;
            if ($isAttendEvent && $event) {
                $existingAttend = MemberActivity::where('user_id', $this->member->id)
                    ->where('activity_type_id', $config->activity_type_id)
                    ->where('event_id', $event->id)
                    ->first();

                $isVoided = ($existingAttend?->metadata['status'] ?? null) === 'void';

                if ($existingAttend && ! $isVoided) {
                    $this->error = "{$this->member->name} has already been recorded as attending \"{$event->title}\".";
                    $this->submitting = false;
                    return;
                }
            }

            $activityAt = Carbon::createFromFormat('Y-m-d\TH:i', $this->activityDateTime);

            DB::transaction(function () use ($config, $activityAt, $event, $isAttendEvent, $existingAttend) {
                $description = $isAttendEvent && $event
                    ? "Member attend event at {$event->title}"
                    : $config->description;

                $metadata = array_filter([
                    'added_by_admin_id' => auth()->id(),
                    'added_by_admin_name' => auth()->user()->name,
                    'added_at' => now()->toIso8601String(),
                    'access_type' => 'manual_addition_by_admin',
                    'device_info' => request()->header('User-Agent'),
                    'ip_address' => request()->ip(),
                    'qr_code' => $this->member->qr_code,
                    'event_id' => $event?->id,
                    'event_code' => $event?->event_code,
                ], fn ($value) => $value !== null);

                if ($existingAttend) {
                    // Revive the voided row in place so the unique constraint is respected.
                    $existingAttend->update([
                        'location_id' => $event?->location_id ?? $config->location_id ?? 1,
                        'amenity_id' => $config->amenity_id,
                        'activity_time' => $activityAt,
                        'description' => $description,
                        'metadata' => $metadata,
                    ]);
                    $memberActivity = $existingAttend->fresh();
                } else {
                    $memberActivity = MemberActivity::create([
                        'user_id' => $this->member->id,
                        'activity_type_id' => $config->activity_type_id,
                        'location_id' => $event?->location_id ?? $config->location_id ?? 1,
                        'amenity_id' => $config->amenity_id,
                        'event_id' => $event?->id,
                        'activity_time' => $activityAt,
                        'description' => $description,
                        'metadata' => $metadata,
                    ]);
                }

                PointLog::create([
                    'user_id' => $this->member->id,
                    'member_activity_id' => $memberActivity->id,
                    'point_system_config_id' => $config->id,
                    'activity_type_id' => $config->activity_type_id,
                    'location_id' => $event?->location_id ?? $config->location_id,
                    'amenity_id' => $config->amenity_id,
                    'points' => $config->points,
                    'description' => $description,
                    'awarded_at' => $activityAt,
                ]);

                $this->member->increment('total_points', $config->points);

                // Mirror EventQrCodeModal: ensure an EventRegistration exists and is
                // marked attended so admin-recorded attendance is reflected in the
                // event's registrant list. `type` must be one of the values allowed
                // by the `event_registrations.type` ENUM (see migration
                // 2026_01_29_155238_add_external_scanner_to_event_registrations_type_enum).
                if ($isAttendEvent && $event) {
                    $registration = EventRegistration::firstOrCreate(
                        [
                            'user_id' => $this->member->id,
                            'event_id' => $event->id,
                        ],
                        [
                            'type' => 'manual',
                            'status' => 'attended',
                            'registered_at' => $activityAt,
                            'attended_at' => $activityAt,
                        ]
                    );

                    if ($registration->status !== 'attended') {
                        $registration->update([
                            'status' => 'attended',
                            'attended_at' => $activityAt,
                        ]);
                    }
                }

                Log::info('Admin manually added activity', [
                    'admin_id' => auth()->id(),
                    'member_id' => $this->member->id,
                    'point_system_config_id' => $config->id,
                    'points' => $config->points,
                    'event_id' => $event?->id,
                    'member_activity_id' => $memberActivity->id,
                    'revived_voided_activity' => (bool) $existingAttend,
                ]);
            });

            $displayName = $config->activityType?->description ?? $config->description ?? 'Activity';
            $eventSuffix = $isAttendEvent && $event ? " for \"{$event->title}\"" : '';
            $this->successMessage = "{$displayName}{$eventSuffix} added successfully. {$config->points} points awarded.";
            $this->dispatch('activity-updated');
        } catch (\Exception $e) {
            Log::error('Failed to add manual activity', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->error = 'Failed to add activity: ' . ($e->getMessage() ?? 'Unknown error.');
        } finally {
            $this->submitting = false;
        }
    }

    public function getPointSystemConfigsProperty()
    {
        return PointSystemConfig::with(['activityType', 'location'])
            ->where('is_active', true)
            ->orderBy('points', 'desc')
            ->get();
    }

    /**
     * Events available to attach when manually adding a `member_attend_event`
     * activity. Sorted with most recent first so admins can quickly find both
     * upcoming and recently-finished events when backfilling attendance.
     */
    public function getEventsProperty()
    {
        return Event::orderByRaw('COALESCE(start_date, created_at) DESC')->get();
    }

    public function render()
    {
        return view('livewire.members.add-activity-modal');
    }
}
