<?php

namespace App\Livewire\Admin;

use App\Models\Location;
use Livewire\Component;

class LocationPerformance extends Component
{
    public function getLocationDataProperty()
    {
        $locations = Location::where('is_active', true)
            ->withCount(['memberActivities' => function($query) {
                $query->where('activity_time', '>=', now()->subDays(30));
            }])
            ->orderBy('member_activities_count', 'desc')
            ->take(10)
            ->get();

        if ($locations->isEmpty()) {
            return [
                'labels' => [],
                'data' => [],
            ];
        }

        return [
            'labels' => $locations->pluck('name')->toArray(),
            'data' => $locations->pluck('member_activities_count')->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.location-performance', [
            'locationData' => $this->locationData,
        ]);
    }
}
