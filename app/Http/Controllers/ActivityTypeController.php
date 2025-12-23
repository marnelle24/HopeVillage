<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use Illuminate\Http\JsonResponse;

class ActivityTypeController extends Controller
{
    /**
     * Get all activity types
     */
    public function index(): JsonResponse
    {
        $activityTypes = ActivityType::where('is_active', true)->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $activityTypes,
        ]);
    }
}

