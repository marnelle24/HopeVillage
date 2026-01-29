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
        DB::statement("ALTER TABLE event_registrations MODIFY COLUMN type ENUM('app', 'event_qr_code', 'user_qr_code', 'manual', 'external_scanner') NOT NULL DEFAULT 'app'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE event_registrations MODIFY COLUMN type ENUM('app', 'event_qr_code', 'user_qr_code', 'manual') NOT NULL DEFAULT 'app'");
    }
};
