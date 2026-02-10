<?php

namespace App\Livewire\Members;

use App\Models\PointLog;
use App\Models\MemberActivity;
use App\Models\EventRegistration;
use App\Services\PointsService;
use Livewire\Component;
use Illuminate\Support\Str;
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
        $activityTypeName = $this->memberActivity->activityType?->name;
        $meta = $this->memberActivity->metadata ?? [];

        $pointsToDeduct = 0;
        if ($pointLog && $pointLog->points > 0) {
            $pointsToDeduct = min($pointLog->points, (int) $user->total_points);
        }

        $randomCode = Str::random(8);

        DB::transaction(function () use ($pointLog, $user, $pointsToDeduct, $randomCode, $activityTypeName, $meta) {
            // 1. Points reversal (existing behaviour)
            if ($pointsToDeduct > 0 && $pointLog) {
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
            }

            // 2. Register or attend event: delete user from event_registrations
            $eventActivityNames = [
                PointsService::ACTIVITY_EVENT_JOIN,   // member_join_event (register)
                PointsService::ACTIVITY_EVENT_ATTEND, // member_attend_event (attend)
                'member_attend_event',                // legacy from EventQrCodeModal
            ];
            if (in_array($activityTypeName, $eventActivityNames, true)) {
                $eventId = $meta['event_id'] ?? null;
                if ($eventId) {
                    EventRegistration::query()
                        ->where('user_id', $user->id)
                        ->where('event_id', $eventId)
                        ->delete();
                    Log::info('Activity voided: event registration removed', [
                        'member_activity_id' => $this->memberActivity->id,
                        'user_id' => $user->id,
                        'event_id' => $eventId,
                    ]);
                }
            }

            // 3. Claim or redeem merchant voucher: remove user from user_voucher
            $merchantVoucherActivityNames = [
                PointsService::ACTIVITY_VOUCHER_CLAIM,  // member_claim_voucher
                PointsService::ACTIVITY_VOUCHER_REDEEM, // member_redeem_voucher
            ];
            if (in_array($activityTypeName, $merchantVoucherActivityNames, true)) {
                $voucherId = $meta['voucher_id'] ?? null;
                if ($voucherId) {
                    $user->vouchers()->detach($voucherId);
                    Log::info('Activity voided: user_voucher pivot removed', [
                        'member_activity_id' => $this->memberActivity->id,
                        'user_id' => $user->id,
                        'voucher_id' => $voucherId,
                    ]);
                }
            }

            // 4. Claim or redeem admin voucher: remove user from user_admin_voucher
            $adminVoucherActivityNames = [
                PointsService::ACTIVITY_ADMIN_VOUCHER_CLAIM, // member_claim_admin_voucher
            ];
            if (in_array($activityTypeName, $adminVoucherActivityNames, true)) {
                $adminVoucherId = $meta['admin_voucher_id'] ?? null;
                if ($adminVoucherId) {
                    $user->adminVouchers()->detach($adminVoucherId);
                    Log::info('Activity voided: user_admin_voucher pivot removed', [
                        'member_activity_id' => $this->memberActivity->id,
                        'user_id' => $user->id,
                        'admin_voucher_id' => $adminVoucherId,
                    ]);
                }
            }
        });

        if ($pointsToDeduct > 0) {
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

        $this->showMessage = true;
        $this->dispatch('activity-updated');
    }

    public function render()
    {
        return view('livewire.members.set-activity-void-button');
    }
}
