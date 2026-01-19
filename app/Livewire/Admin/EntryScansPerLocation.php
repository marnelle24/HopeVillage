<?php

namespace App\Livewire\Admin;

use App\Models\ActivityType;
use App\Models\MemberActivity;
use Livewire\Component;

class EntryScansPerLocation extends Component
{
    public function getActivityTypesDataProperty()
    {
        // Get all active activity types
        $activityTypes = ActivityType::where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($activityTypes->isEmpty()) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // Get last 30 days of member activities
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        // Get all activities in the last 30 days
        $activities = MemberActivity::whereBetween('activity_time', [$startDate, $endDate])
            ->with('activityType')
            ->get();

        // Create array with all 30 days, filling missing days with 0
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayName = now()->subDays($i)->format('M d');
            $labels[] = $dayName;
        }

        // Prepare datasets for each activity type
        $datasets = [];
        $colors = [
            ['bg' => 'rgba(59, 130, 246, 0.8)', 'border' => 'rgb(59, 130, 246)'],
            ['bg' => 'rgba(16, 185, 129, 0.8)', 'border' => 'rgb(16, 185, 129)'],
            ['bg' => 'rgba(245, 158, 11, 0.8)', 'border' => 'rgb(245, 158, 11)'],
            ['bg' => 'rgba(239, 68, 68, 0.8)', 'border' => 'rgb(239, 68, 68)'],
            ['bg' => 'rgba(139, 92, 246, 0.8)', 'border' => 'rgb(139, 92, 246)'],
            ['bg' => 'rgba(236, 72, 153, 0.8)', 'border' => 'rgb(236, 72, 153)'],
            ['bg' => 'rgba(20, 184, 166, 0.8)', 'border' => 'rgb(20, 184, 166)'],
            ['bg' => 'rgba(251, 146, 60, 0.8)', 'border' => 'rgb(251, 146, 60)'],
            ['bg' => 'rgba(99, 102, 241, 0.8)', 'border' => 'rgb(99, 102, 241)'],
            ['bg' => 'rgba(168, 85, 247, 0.8)', 'border' => 'rgb(168, 85, 247)'],
        ];

        $colorIndex = 0;

        foreach ($activityTypes as $activityType) {
            $data = [];
            
            // Count activities for this type for each day
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $count = $activities
                    ->where('activity_type_id', $activityType->id)
                    ->filter(function ($activity) use ($date) {
                        return $activity->activity_time->format('Y-m-d') === $date;
                    })
                    ->count();
                $data[] = $count;
            }

            // Only include activity types that have at least one activity
            if (array_sum($data) > 0) {
                $color = $colors[$colorIndex % count($colors)];
                $datasets[] = [
                    'label' => $activityType->name,
                    'data' => $data,
                    'borderColor' => $color['border'],
                    'backgroundColor' => $color['bg'],
                    'tension' => 0.4,
                    'fill' => false,
                ];
                $colorIndex++;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    public function render()
    {
        return view('livewire.admin.entry-scans-per-location', [
            'activityTypesData' => $this->activityTypesData,
        ]);
    }
}
