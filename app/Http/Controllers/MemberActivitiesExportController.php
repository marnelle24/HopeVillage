<?php

namespace App\Http\Controllers;

use App\Models\MemberActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberActivitiesExportController extends Controller
{
    /**
     * Export filtered member activities as CSV.
     * Query params: search, activity_type, date (all|today|week|month)
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $query = MemberActivity::query()
            ->with(['user', 'activityType', 'pointLog']);

        $search = $request->query('search', '');
        if ($search !== '') {
            $s = '%' . $search . '%';
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', function ($userQuery) use ($s) {
                    $userQuery->where('name', 'like', $s)
                        ->orWhere('email', 'like', $s)
                        ->orWhere('fin', 'like', $s);
                })
                ->orWhere('description', 'like', $s);
            });
        }

        $activityType = $request->query('activity_type', '');
        if ($activityType !== '') {
            $query->where('activity_type_id', $activityType);
        }

        $dateFilter = $request->query('date', 'all');
        if ($dateFilter === 'today') {
            $query->whereDate('activity_time', today());
        } elseif ($dateFilter === 'week') {
            $query->where('activity_time', '>=', now()->subWeek());
        } elseif ($dateFilter === 'month') {
            $query->where('activity_time', '>=', now()->subMonth());
        }

        $activities = $query->orderByDesc('activity_time')->get();

        $filename = 'member-activities-' . now()->format('Y-m-d-His') . '.csv';

        return Response::streamDownload(function () use ($activities) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Member Name',
                'Email',
                'QR Code / FIN',
                'Activity Type',
                'Points',
                'Description',
                'Timestamp',
            ]);

            foreach ($activities as $activity) {
                $user = $activity->user;
                fputcsv($handle, [
                    $user?->name ?? 'Deleted User',
                    $user?->email ?? '',
                    $user?->qr_code ?? $user?->fin ?? '',
                    $activity->activityType?->name ?? 'N/A',
                    $activity->pointLog?->points ?? 0,
                    $activity->description ?? '',
                    $activity->activity_time->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
