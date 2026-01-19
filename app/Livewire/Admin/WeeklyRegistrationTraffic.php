<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class WeeklyRegistrationTraffic extends Component
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

    public function render()
    {
        return view('livewire.admin.weekly-registration-traffic', [
            'weeklyRegistrations' => $this->weeklyRegistrations,
        ]);
    }
}
