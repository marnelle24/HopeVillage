<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Get settings records
     * Optional query parameter "key" - if empty, fetch all; if specified, return matching setting
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $key = $request->query('key');

        // If key parameter is provided and not empty
        if (!empty($key)) {
            $setting = Setting::where('key', $key)->first();

            // If no setting found with the specified key, return 404
            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found',
                    'error' => "No setting found with key: {$key}",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $setting,
            ]);
        }

        // If no key parameter or empty, return all settings
        $settings = Setting::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }
}
