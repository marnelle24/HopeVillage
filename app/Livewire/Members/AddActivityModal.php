<?php

namespace App\Livewire\Members;

use App\Models\MemberActivity;
use App\Models\PointLog;
use App\Models\PointSystemConfig;
use App\Models\User;
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

    public bool $submitting = false;

    public ?string $error = null;

    public ?string $successMessage = null;

    protected $listeners = [
        'openAddActivityModal' => 'open',
    ];

    protected function rules(): array
    {
        return [
            'pointSystemConfigId' => ['required', 'string', 'exists:point_system_configs,id'],
            'activityDateTime' => ['required', 'string', 'date'],
        ];
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
        $this->activityDateTime = now()->format('Y-m-d\TH:i');
        $this->error = null;
        $this->successMessage = null;
        $this->open = true;
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
                
            $activityAt = Carbon::createFromFormat('Y-m-d\TH:i', $this->activityDateTime);

            DB::transaction(function () use ($config, $activityAt) {
                $memberActivity = MemberActivity::create([
                    'user_id' => $this->member->id,
                    'activity_type_id' => $config->activity_type_id,
                    'location_id' => $config->location_id ?? 1,
                    'amenity_id' => $config->amenity_id,
                    'activity_time' => $activityAt,
                    'description' => $config->description,
                    'metadata' => [
                        'added_by_admin_id' => auth()->id(),
                        'added_by_admin_name' => auth()->user()->name,
                        'added_at' => now()->toIso8601String(),
                        'access_type' => 'manual_addition_by_admin',
                        'device_info' => request()->header('User-Agent'),
                        'ip_address' => request()->ip(),
                        'qr_code' => $this->member->qr_code,
                    ],
                ]);

                PointLog::create([
                    'user_id' => $this->member->id,
                    'member_activity_id' => $memberActivity->id,
                    'point_system_config_id' => $config->id,
                    'activity_type_id' => $config->activity_type_id,
                    'location_id' => $config->location_id,
                    'amenity_id' => $config->amenity_id,
                    'points' => $config->points,
                    'description' => $config->description,
                    'awarded_at' => $activityAt,
                ]);

                $this->member->increment('total_points', $config->points);

                Log::info('Admin manually added activity', [
                    'admin_id' => auth()->id(),
                    'member_id' => $this->member->id,
                    'point_system_config_id' => $config->id,
                    'points' => $config->points,
                ]);
            });

            $displayName = $config->activityType?->description ?? $config->description ?? 'Activity';
            $this->successMessage = "{$displayName} added successfully. {$config->points} points awarded.";
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

    public function render()
    {
        return view('livewire.members.add-activity-modal');
    }
}
