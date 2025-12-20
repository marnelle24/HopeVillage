<?php

namespace App\Http\Controllers;

use App\Services\TwilioWhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppValidationController extends Controller
{
    public function check(Request $request, TwilioWhatsAppService $whatsAppService): JsonResponse
    {
        try {
            $request->validate([
                'phone' => 'required|string'
            ]);

            // If service is not enabled, return a neutral response (200 OK)
            if (!$whatsAppService->enabled()) {
                return response()->json([
                    'is_whatsapp' => null,
                    'message' => 'WhatsApp validation service is not configured'
                ]);
            }

            // Validate phone number format using Twilio Lookup
            // Note: Twilio validates phone number format but cannot confirm WhatsApp without sending
            $validationResult = $whatsAppService->validatePhoneNumber($request->phone);
            
            // Ensure we got a valid result structure
            if (!is_array($validationResult)) {
                Log::warning('WhatsApp validation returned invalid result format', [
                    'phone' => $request->input('phone'),
                    'result' => $validationResult,
                ]);
                return response()->json([
                    'is_whatsapp' => null,
                    'error' => 'Validation service returned an invalid response'
                ]);
            }
            
            if ($validationResult['valid'] ?? false) {
                return response()->json([
                    'is_whatsapp' => true,
                    'formatted' => $validationResult['formatted'] ?? null,
                    'message' => 'Phone number format is valid. Note: Actual WhatsApp registration is verified when sending messages.'
                ]);
            } else {
                return response()->json([
                    'is_whatsapp' => false,
                    'error' => $validationResult['error'] ?? 'Invalid phone number format'
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as 422
            return response()->json([
                'is_whatsapp' => null,
                'error' => 'Invalid request. Please provide a valid phone number.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('WhatsApp validation check failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'phone' => $request->input('phone'),
            ]);

            // Return 200 with error message to avoid breaking the frontend
            // Frontend handles errors based on the response structure, not status code
            return response()->json([
                'is_whatsapp' => null,
                'error' => 'An error occurred while validating the phone number. Please try again later.'
            ]);
        }
    }
}

