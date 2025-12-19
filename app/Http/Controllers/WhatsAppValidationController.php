<?php

namespace App\Http\Controllers;

use App\Services\TwilioWhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppValidationController extends Controller
{
    public function check(Request $request, TwilioWhatsAppService $whatsAppService): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        // If service is not enabled, return a neutral response
        if (!$whatsAppService->enabled()) {
            return response()->json([
                'is_whatsapp' => null,
                'message' => 'WhatsApp validation service is not configured'
            ]);
        }

        // Twilio validates phone number format but cannot confirm WhatsApp without sending
        // We validate the format and return true if valid
        $isValid = $whatsAppService->isValidWhatsAppNumber($request->phone);
        
        if ($isValid) {
            $validationResult = $whatsAppService->validatePhoneNumber($request->phone);
            return response()->json([
                'is_whatsapp' => true,
                'formatted' => $validationResult['formatted'] ?? null,
                'message' => 'Phone number format is valid. Note: Actual WhatsApp registration is verified when sending messages.'
            ]);
        } else {
            $validationResult = $whatsAppService->validatePhoneNumber($request->phone);
            return response()->json([
                'is_whatsapp' => false,
                'error' => $validationResult['error'] ?? 'Invalid phone number format'
            ]);
        }
    }
}

