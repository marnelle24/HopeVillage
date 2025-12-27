<?php

namespace App\Livewire;

use App\Models\User;
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
    public $redeemerQrCode = null;
    public $redeemer = null;
    public $redeemerVoucherStatus = null;
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

    public function open($voucherCode = null, $redeemerQrCode = null)
    {
        if ($voucherCode) {
            $this->voucherCode = $voucherCode;
        }
        if ($redeemerQrCode) {
            $this->redeemerQrCode = $redeemerQrCode;
            $this->loadRedeemer();
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
        $this->redeemer = null;
        $this->redeemerQrCode = null;
    }
    
    public function loadRedeemer()
    {
        if ($this->redeemerQrCode) {
            // Normalize the QR code (trim and uppercase)
            $normalizedQrCode = strtoupper(trim($this->redeemerQrCode));
            
            // Try to find user by qr_code or fin
            $this->redeemer = User::where('qr_code', $normalizedQrCode)
                ->orWhere('fin', $normalizedQrCode)
                ->first();
            
            // Check voucher status for redeemer if voucher is loaded
            if ($this->redeemer && $this->voucher) {
                $pivot = $this->redeemer->vouchers()
                    ->where('vouchers.id', $this->voucher->id)
                    ->first();
                $this->redeemerVoucherStatus = $pivot?->pivot->status ?? null;
            }
        }
    }

    public function loadVoucher()
    {
        if ($this->voucherCode) {
            // Normalize the voucher code (trim and uppercase)
            $normalizedCode = strtoupper(trim($this->voucherCode));
            
            // Voucher code already includes VOU- prefix in database
            $this->voucher = Voucher::with('merchant')
                ->where('voucher_code', $normalizedCode)
                ->first();
            
            if (!$this->voucher) {
                $this->error = 'Voucher not found.';
                return;
            }
            
            // Only automatically process voucher redemption for members
            // Merchants will see voucher info but need to process differently
            $user = auth()->user();
            if ($user && $user->isMember()) {
                $this->processVoucherRedemption();
            }
            
            // If redeemer is loaded, check their voucher status
            if ($this->redeemer) {
                $pivot = $this->redeemer->vouchers()
                    ->where('vouchers.id', $this->voucher->id)
                    ->first();
                $this->redeemerVoucherStatus = $pivot?->pivot->status ?? null;
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
    
    public function processRedeemerVoucherRedemption()
    {
        $currentUser = auth()->user();
        
        // Only merchants can process redemption for redeemers
        if (!$currentUser || !$currentUser->isMerchantUser()) {
            $this->error = 'Only merchants can process voucher redemption.';
            return;
        }
        
        if (!$this->redeemer) {
            $this->error = 'Redeemer information not found.';
            return;
        }
        
        if (!$this->voucher) {
            $this->error = 'Voucher not found.';
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
            DB::transaction(function () use ($currentUser) {
                // Check if voucher is claimed by the redeemer
                $pivot = $this->redeemer->vouchers()
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
                $this->redeemer->vouchers()->updateExistingPivot($this->voucher->id, [
                    'status' => 'redeemed',
                    'redeemed_at' => now(),
                ]);
                
                // Update voucher usage count
                $this->voucher->increment('usage_count');
                
                // Award points for voucher redemption
                $pointsBefore = $this->redeemer->total_points;
                $pointsAwarded = 0;
                
                app(PointsService::class)->awardVoucherRedeem($this->redeemer, $this->voucher);
                
                $this->redeemer->refresh();
                $pointsAwarded = $this->redeemer->total_points - $pointsBefore;
                
                Log::info('Voucher redeemed by merchant', [
                    'merchant_id' => $currentUser->id,
                    'member_fin' => $this->redeemer->fin,
                    'voucher_code' => $this->voucher->voucher_code,
                    'voucher_id' => $this->voucher->id,
                    'points_awarded' => $pointsAwarded,
                ]);
                
                // Set success message
                $this->success = true;
                $this->successMessage = "Voucher redeemed successfully!";
                
                // Update redeemer voucher status
                $this->redeemerVoucherStatus = 'redeemed';
                
                $this->processing = false;
            });
        } catch (\Exception $e) {
            Log::error('Failed to redeem voucher by merchant', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->error = 'Failed to redeem voucher: ' . ($e->getMessage() ?? 'Unknown error');
            $this->processing = false;
        }
    }

    public function render()
    {
        return view('livewire.voucher-qr-code-modal');
    }
}
