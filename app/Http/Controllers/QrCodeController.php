<?php

namespace App\Http\Controllers;

use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrCodeController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Get QR code image for the authenticated member
     */
    public function show()
    {
        $user = Auth::user();

        if (!$user || !$user->isMember() || !$user->qr_code) {
            abort(404, 'QR code not found');
        }

        $qrCodeDataUri = $this->qrCodeService->generateQrCodeImage($user->qr_code, 300);
        
        return response()->json([
            'qr_code' => $user->qr_code,
            'image' => $qrCodeDataUri,
        ]);
    }

    /**
     * Get QR code image as full size for display
     */
    public function fullSize()
    {
        $user = Auth::user();

        if (!$user || !$user->isMember() || !$user->qr_code) {
            abort(404, 'QR code not found');
        }

        $qrCodeDataUri = $this->qrCodeService->generateQrCodeImage($user->qr_code, 600);
        
        return response()->json([
            'qr_code' => $user->qr_code,
            'image' => $qrCodeDataUri,
        ]);
    }
}

