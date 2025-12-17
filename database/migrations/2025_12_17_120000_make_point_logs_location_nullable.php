<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('point_logs')) {
            return;
        }

        // Avoid requiring doctrine/dbal by using raw SQL (MySQL).
        // Make location_id nullable so we can log non-location activities (e.g., voucher claim/redeem).
        DB::statement('ALTER TABLE point_logs MODIFY location_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('point_logs')) {
            return;
        }

        DB::statement('ALTER TABLE point_logs MODIFY location_id BIGINT UNSIGNED NOT NULL');
    }
};


