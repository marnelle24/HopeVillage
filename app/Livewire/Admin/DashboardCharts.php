<?php

namespace App\Livewire\Admin;

use App\Models\ActivityType;
use App\Models\MemberActivity;
use App\Models\User;
use Livewire\Component;

class DashboardCharts extends Component
{
    public function getWeeklyRegistrationsProperty()
    {
        // Get last 7 days of member registrations
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $registrations = User::where('user_type', 'member')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Create array with all 7 days, filling missing days with 0
        $labels = [];
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayName = now()->subDays($i)->format('D');
            $labels[] = $dayName;
            
            $registration = $registrations->firstWhere('date', $date);
            $data[] = $registration ? $registration->count : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function getEntryScansPerLocationProperty()
    {
        // Get entry activity types (could be 'ENTRY', 'member_entry_location', or similar)
        $entryActivityTypes = ActivityType::where(function ($query) {
            $query->where('name', 'ENTRY')
                ->orWhere('name', 'member_entry_location')
                ->orWhere('name', 'like', '%entry%');
        })->pluck('id');

        if ($entryActivityTypes->isEmpty()) {
            return [
                'labels' => [],
                'data' => [],
            ];
        }

        // Get entry scans grouped by location for the last 30 days
        $scans = MemberActivity::whereIn('activity_type_id', $entryActivityTypes)
            ->where('activity_time', '>=', now()->subDays(30))
            ->with('location')
            ->get()
            ->filter(function ($activity) {
                return $activity->location !== null;
            })
            ->groupBy(function ($activity) {
                return $activity->location->name ?? 'Unknown';
            })
            ->map(function ($activities) {
                return $activities->count();
            })
            ->sortDesc()
            ->take(10); // Top 10 locations

        // Truncate labels to 3 words max with ellipsis
        $labels = $scans->keys()->map(function ($label) {
            $words = explode(' ', $label);
            if (count($words) > 3) {
                return implode(' ', array_slice($words, 0, 3)) . '...';
            }
            return $label;
        })->toArray();

        return [
            'labels' => $labels,
            'data' => $scans->values()->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard-charts', [
            'weeklyRegistrations' => $this->weeklyRegistrations,
            'entryScansPerLocation' => $this->entryScansPerLocation,
        ]);
    }
}

