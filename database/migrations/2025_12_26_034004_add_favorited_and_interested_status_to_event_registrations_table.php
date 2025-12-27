<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE event_registrations MODIFY COLUMN status ENUM('favorited', 'interested', 'registered', 'attended', 'cancelled', 'no_show') NOT NULL DEFAULT 'registered'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE event_registrations MODIFY COLUMN status ENUM('registered', 'attended', 'cancelled', 'no_show') NOT NULL DEFAULT 'registered'");
    }
};
