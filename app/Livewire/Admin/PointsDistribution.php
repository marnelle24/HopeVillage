<?php

namespace App\Livewire\Admin;

use App\Models\PointLog;
use Livewire\Component;

class PointsDistribution extends Component
{
    public function getPointsDataProperty()
    {
        $pointsByActivity = PointLog::selectRaw('activity_type_id, SUM(points) as total_points')
            ->where('awarded_at', '>=', now()->subDays(30))
            ->groupBy('activity_type_id')
            ->with('activityType')
            ->get()
            ->filter(function($item) {
                return $item->activityType && $item->total_points > 0;
            })
            ->sortByDesc('total_points')
            ->take(8);

        if ($pointsByActivity->isEmpty()) {
            return [
                'labels' => [],
                'data' => [],
            ];
        }

        return [
            'labels' => $pointsByActivity->pluck('activityType.name')->toArray(),
            'data' => $pointsByActivity->pluck('total_points')->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.points-distribution', [
            'pointsData' => $this->pointsData,
        ]);
    }
}
