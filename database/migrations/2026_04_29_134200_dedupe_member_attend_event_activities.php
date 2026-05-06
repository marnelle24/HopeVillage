<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $attendActivityTypeId = DB::table('activity_types')
            ->where('name', 'member_attend_event')
            ->value('id');

        if (! $attendActivityTypeId) {
            return;
        }

        $duplicateGroups = DB::table('member_activities')
            ->select(['user_id', 'activity_type_id', 'event_id', DB::raw('COUNT(*) as duplicate_count')])
            ->where('activity_type_id', $attendActivityTypeId)
            ->whereNotNull('event_id')
            ->groupBy('user_id', 'activity_type_id', 'event_id')
            ->having('duplicate_count', '>', 1)
            ->get();

        foreach ($duplicateGroups as $group) {
            $activityIds = DB::table('member_activities')
                ->where('user_id', $group->user_id)
                ->where('activity_type_id', $group->activity_type_id)
                ->where('event_id', $group->event_id)
                ->orderBy('id')
                ->pluck('id')
                ->values();

            $keepId = $activityIds->first();
            $duplicateIds = $activityIds->slice(1)->all();

            if (! $keepId || empty($duplicateIds)) {
                continue;
            }

            $pointsToRollback = (int) DB::table('point_logs')
                ->whereIn('member_activity_id', $duplicateIds)
                ->sum('points');

            DB::table('point_logs')
                ->whereIn('member_activity_id', $duplicateIds)
                ->delete();

            if ($pointsToRollback > 0) {
                DB::table('users')
                    ->where('id', $group->user_id)
                    ->decrement('total_points', $pointsToRollback);
            }

            DB::table('member_activities')
                ->whereIn('id', $duplicateIds)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: dedupe is destructive by nature.
    }
};
