<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Get all active/published events
     * 
     * Query parameters:
     * - status: Filter by status (default: 'published')
     * - location_id: Filter by location ID
     * - search: Search in title, description, venue
     * - upcoming: Only show upcoming events (default: true)
     * - limit: Number of results (optional)
     */
    public function getEvents(Request $request): JsonResponse
    {
        $query = Event::query()
            ->where('status', $request->input('status', 'published'))
            // Only show events from locations that are not soft deleted
            ->whereHas('location', fn ($q) => $q->whereNull('deleted_at'));

        // Filter by location_id if provided
        if ($request->has('location_id')) {
            $query->where('location_id', $request->input('location_id'));
        }

        // Search filter
        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('venue', 'like', '%' . $search . '%')
                    ->orWhereHas('location', function ($lq) use ($search) {
                        $lq->whereNull('deleted_at')
                            ->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter upcoming events (default: true)
        $upcoming = $request->has('upcoming') ? filter_var($request->input('upcoming'), FILTER_VALIDATE_BOOLEAN) : true;
        if ($upcoming) {
            $query->where('end_date', '>=', now());
        }

        // Load relationships
        $query->with(['location', 'media'])
            ->withCount('registrations')
            ->orderBy('start_date', 'asc');

        // Apply limit if provided
        if ($request->has('limit')) {
            $events = $query->limit((int) $request->input('limit'))->get();
        } else {
            $events = $query->get();
        }

        // Transform events for API response
        $events = $events->map(function ($event) {
            // Get thumbnail URL
            $thumbnailUrl = $event->thumbnail_url;
            
            // If no thumbnail, try to generate map thumbnail from location
            if (!$thumbnailUrl && $event->location) {
                $apiKey = config('services.google_maps.api_key');
                if ($apiKey) {
                    $location = $event->location;
                    if (!empty($location->address)) {
                        $address = urlencode(trim($location->address . ', ' . $location->city . ', ' . $location->province . ', ' . $location->postal_code, ', '));
                        $thumbnailUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$address}&zoom=15&size=400x200&markers=color:red|{$address}&key={$apiKey}";
                    }
                }
            }

            return [
                'id' => $event->id,
                'event_code' => $event->event_code,
                'title' => $event->title,
                'description' => $event->description,
                'venue' => $event->venue,
                'start_date' => $event->start_date?->toIso8601String(),
                'end_date' => $event->end_date?->toIso8601String(),
                'max_participants' => $event->max_participants,
                'status' => $event->status,
                'thumbnail_url' => $thumbnailUrl,
                'registrations_count' => $event->registrations_count,
                'location' => $event->location ? [
                    'id' => $event->location->id,
                    'location_code' => $event->location->location_code,
                    'name' => $event->location->name,
                    'address' => $event->location->address,
                    'city' => $event->location->city,
                    'province' => $event->location->province,
                    'postal_code' => $event->location->postal_code,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $events,
            'count' => $events->count(),
        ]);
    }
}

