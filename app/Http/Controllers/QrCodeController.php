<?php

namespace App\Http\Controllers;

use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        try {
            $user = Auth::user();

            if (!$user || !$user->isMember() || !$user->qr_code) {
                return response()->json([
                    'error' => 'QR code not found'
                ], 404);
            }

            $qrCodeDataUri = $this->qrCodeService->generateQrCodeImage($user->qr_code, 300);
            
            return response()->json([
                'qr_code' => $user->qr_code,
                'image' => $qrCodeDataUri,
            ]);
        } catch (\Exception $e) {
            Log::error('QR code generation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Failed to generate QR code. Please try again later.'
            ], 500);
        }
    }

    /**
     * Get QR code image as full size for display
     */
    public function fullSize()
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->isMember() || !$user->qr_code) {
                return response()->json([
                    'error' => 'QR code not found'
                ], 404);
            }

            $qrCodeDataUri = $this->qrCodeService->generateQrCodeImage($user->qr_code, 600);
            
            return response()->json([
                'qr_code' => $user->qr_code,
                'image' => $qrCodeDataUri,
            ]);
        } catch (\Exception $e) {
            Log::error('QR code generation failed (full size)', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Failed to generate QR code. Please try again later.'
            ], 500);
        }
    }
}

