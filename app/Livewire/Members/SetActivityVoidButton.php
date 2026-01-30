<?php

namespace App\Livewire\Members;

use Livewire\Component;
use App\Models\PointLog;
use Illuminate\Support\Str;
use App\Models\MemberActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetActivityVoidButton extends Component
{
    public MemberActivity $memberActivity;

    public $showMessage = false;

    public function setAsVoid(): void
    {
        $this->showMessage = false;

        if (! auth()->user()?->isAdmin()) {
            session()->flash('error', 'You do not have permission to set activity as void.');
            return;
        }

        if (($this->memberActivity->metadata['status'] ?? null) === 'void') {
            return;
        }

        $metadata = array_merge($this->memberActivity->metadata ?? [], ['status' => 'void']);
        $this->memberActivity->update(['metadata' => $metadata]);

        $pointLog = $this->memberActivity->pointLog;
        $user = $this->memberActivity->user;

        if ($pointLog && $pointLog->points > 0) {
            $pointsToDeduct = min($pointLog->points, (int) $user->total_points);

            $randomCode = Str::random(8);

            if ($pointsToDeduct > 0) {
                DB::transaction(function () use ($pointLog, $user, $pointsToDeduct, $randomCode) {
                    PointLog::query()->create([
                        'user_id' => $user->id,
                        'member_activity_id' => $this->memberActivity->id,
                        'point_system_config_id' => $pointLog->point_system_config_id,
                        'activity_type_id' => $pointLog->activity_type_id,
                        'location_id' => $pointLog->location_id,
                        'amenity_id' => $pointLog->amenity_id,
                        'points' => -$pointsToDeduct,
                        'description' => 'Points reversed - activity voided (Member Activity #'.$this->memberActivity->id.') - with trx code: '.$randomCode,
                        'awarded_at' => now(),
                    ]);

                    $user->decrement('total_points', $pointsToDeduct);
                });

                Log::info('Activity voided: points deducted from user wallet', [
                    'admin_id' => auth()->id(),
                    'admin_email' => auth()->user()?->email,
                    'member_activity_id' => $this->memberActivity->id,
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'points_deducted' => $pointsToDeduct,
                    'user_total_points_after' => $user->fresh()->total_points,
                    'voided_at' => now()->toIso8601String(),
                    'trx_code' => $randomCode,
                ]);
            }
        }

        $this->showMessage = true;
        $this->dispatch('activity-updated');
    }

    public function render()
    {
        return view('livewire.members.set-activity-void-button');
    }
}
