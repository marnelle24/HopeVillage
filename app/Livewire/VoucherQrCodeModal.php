<?php

namespace App\Livewire;

use App\Models\Voucher;
use App\Services\PointsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class VoucherQrCodeModal extends Component
{
    public $voucherCode;
    public $open = false;
    public $voucher = null;
    public $memberFin = null;
    public $processing = false;
    public $error = null;
    public $success = false;
    public $successMessage = null;

    protected $listeners = [
        'openVoucherQrModal' => 'open',
        'closeVoucherQrModal' => 'close',
    ];

    public function mount($voucherCode = null)
    {
        $this->voucherCode = $voucherCode;
        if ($this->voucherCode) {
            $this->loadVoucher();
        }
    }

    public function open($voucherCode = null)
    {
        if ($voucherCode) {
            $this->voucherCode = $voucherCode;
        }
        $this->open = true;
        $this->error = null;
        $this->success = false;
        $this->processing = false;
        $this->memberFin = auth()->user()?->fin;
        
        if ($this->voucherCode) {
            $this->loadVoucher();
        }
    }

    public function close()
    {
        $this->open = false;
        $this->error = null;
        $this->success = false;
        $this->processing = false;
    }

    public function loadVoucher()
    {
        if ($this->voucherCode) {
            // Voucher code already includes VOU- prefix in database
            $this->voucher = Voucher::with('merchant')->where('voucher_code', $this->voucherCode)->first();
            
            // Automatically process voucher redemption
            if ($this->voucher) {
                $this->processVoucherRedemption();
            }
        }
    }
    
    public function processVoucherRedemption()
    {
        $user = auth()->user();
        
        if (!$user || !$user->fin) {
            $this->error = 'Please login to scan QR codes.';
            return;
        }

        if (!$this->voucher) {
            $this->error = 'Voucher not found.';
            return;
        }

        // Check if user is a member
        if ($user->user_type !== 'member') {
            $this->error = 'User is not a member.';
            return;
        }

        // Check if voucher is active
        if (!$this->voucher->is_active) {
            $this->error = 'Voucher is not active.';
            return;
        }

        $this->processing = true;
        $this->error = null;
        $this->success = false;
        $this->successMessage = null;

        try {
            DB::transaction(function () use ($user) {
                // Check if voucher is claimed by this member
                $pivot = $user->vouchers()
                    ->where('vouchers.id', $this->voucher->id)
                    ->first();
                
                if (!$pivot) {
                    $this->error = 'Voucher not claimed by this member. Please claim the voucher first.';
                    $this->processing = false;
                    return;
                }

                // Check if voucher is already redeemed
                if ($pivot->pivot->status === 'redeemed') {
                    $this->error = 'Voucher has already been redeemed.';
                    $this->processing = false;
                    return;
                }

                // Check if voucher status is 'claimed' (required for redemption)
                if ($pivot->pivot->status !== 'claimed') {
                    $this->error = 'Voucher must be claimed before redemption.';
                    $this->processing = false;
                    return;
                }

                // Mark voucher as redeemed
                $user->vouchers()->updateExistingPivot($this->voucher->id, [
                    'status' => 'redeemed',
                    'redeemed_at' => now(),
                ]);

                // Update voucher usage count
                $this->voucher->increment('usage_count');

                // Award points for voucher redemption
                $pointsBefore = $user->total_points;
                $pointsAwarded = 0;
                
                app(PointsService::class)->awardVoucherRedeem($user, $this->voucher);
                
                $user->refresh();
                $pointsAwarded = $user->total_points - $pointsBefore;

                Log::info('Voucher redeemed', [
                    'member_fin' => $user->fin,
                    'voucher_code' => $this->voucher->voucher_code,
                    'voucher_id' => $this->voucher->id,
                    'points_awarded' => $pointsAwarded,
                ]);

                // Set success message
                $this->success = true;
                $this->successMessage = "SUCCESS";
                
                session()->flash('voucher-redeem-success', 'SUCCESS');
                $this->processing = false;
            });
        } catch (\Exception $e) {
            Log::error('Failed to redeem voucher', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->error = 'Failed to redeem voucher: ' . ($e->getMessage() ?? 'Unknown error');
            $this->processing = false;
        }
    }

    public function processScan()
    {
        // This method is kept for backward compatibility but now calls processVoucherRedemption
        $this->processVoucherRedemption();
    }

    public function render()
    {
        return view('livewire.voucher-qr-code-modal');
    }
}
