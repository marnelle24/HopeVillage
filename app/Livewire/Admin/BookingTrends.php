<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;

class BookingTrends extends Component
{
    public function getBookingDataProperty()
    {
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        $bookings = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, status')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('M d');
        }

        $pending = [];
        $confirmed = [];
        $cancelled = [];
        $completed = [];

        foreach ($labels as $index => $label) {
            $date = now()->subDays(29 - $index)->format('Y-m-d');
            $pending[] = $bookings->where('date', $date)->where('status', 'pending')->sum('count');
            $confirmed[] = $bookings->where('date', $date)->where('status', 'confirmed')->sum('count');
            $cancelled[] = $bookings->where('date', $date)->where('status', 'cancelled')->sum('count');
            $completed[] = $bookings->where('date', $date)->where('status', 'completed')->sum('count');
        }

        return [
            'labels' => $labels,
            'pending' => $pending,
            'confirmed' => $confirmed,
            'cancelled' => $cancelled,
            'completed' => $completed,
        ];
    }

    public function render()
    {
        return view('livewire.admin.booking-trends', [
            'bookingData' => $this->bookingData,
        ]);
    }
}
