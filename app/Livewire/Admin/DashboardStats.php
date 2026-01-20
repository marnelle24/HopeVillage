<?php

namespace App\Livewire\Admin;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Location;
use App\Models\Merchant;
use App\Models\PointLog;
use App\Models\Program;
use App\Models\User;
use App\Models\Voucher;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        $totalLocations = Location::count();
        $totalMembers = User::where('user_type', 'member')->count();
        $activeEvents = Event::where('status', 'published')->count();
        $activePrograms = Program::where('status', 'published')->count();
        $totalMerchants = Merchant::count();
        $totalPoints = PointLog::sum('points');
        $activeVouchers = Voucher::where('is_active', true)->count();
        $totalAmenities = Amenity::where('is_active', true)->count();
        
        // Calculate active members (active in last 30 days)
        $activeMembers = User::where('user_type', 'member')
            ->whereHas('memberActivities', function($query) {
                $query->where('activity_time', '>=', now()->subDays(30));
            })
            ->count();
        
        $engagementRate = $totalMembers > 0 
            ? round(($activeMembers / $totalMembers) * 100, 1) 
            : 0;

        // Upcoming events (next 7 days)
        $upcomingEvents = Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays(7))
            ->count();

        return view('livewire.admin.dashboard-stats', [
            'totalLocations' => $totalLocations,
            'totalMembers' => $totalMembers,
            'activeEvents' => $activeEvents,
            'activePrograms' => $activePrograms,
            'totalMerchants' => $totalMerchants,
            'totalPoints' => $totalPoints,
            'activeVouchers' => $activeVouchers,
            'totalAmenities' => $totalAmenities,
            'activeMembers' => $activeMembers,
            'engagementRate' => $engagementRate,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }
}
