<?php

namespace App\Livewire\Member;

use App\Services\QrCodeService;
use Livewire\Component;

class ReferralSystem extends Component
{
    public $referralLink = '';
    public $qrCodeImage = '';

    public function mount()
    {
        $user = auth()->user();
        
        if ($user && $user->qr_code) {
            $this->referralLink = $user->referral_link;
            
            // Generate QR code for the referral link
            $qrCodeService = app(QrCodeService::class);
            $this->qrCodeImage = $qrCodeService->generateQrCodeImage($this->referralLink, 400);
        }
    }


    public function getReferralsProperty()
    {
        $user = auth()->user();
        
        if (!$user) {
            return collect([]);
        }

        return $user->referrals()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.member.referral-system')
            ->layout('layouts.app', [
                'title' => 'Referral System'
            ]);
    }
}
