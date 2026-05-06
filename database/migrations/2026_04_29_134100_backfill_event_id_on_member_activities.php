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

        DB::table('member_activities')
            ->select(['id', 'metadata'])
            ->where('activity_type_id', $attendActivityTypeId)
            ->whereNull('event_id')
            ->orderBy('id')
            ->chunkById(500, function ($rows): void {
                foreach ($rows as $row) {
                    $metadata = is_array($row->metadata) ? $row->metadata : json_decode((string) $row->metadata, true);
                    $eventId = (int) ($metadata['event_id'] ?? 0);

                    if ($eventId > 0) {
                        DB::table('member_activities')
                            ->where('id', $row->id)
                            ->update(['event_id' => $eventId]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op on purpose: keep event_id linkage once backfilled.
    }
};
