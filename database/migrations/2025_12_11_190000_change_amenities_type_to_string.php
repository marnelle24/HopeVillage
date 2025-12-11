<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite already uses string type, no change needed
            return;
        } else {
            // MySQL/MariaDB - Change ENUM to VARCHAR to support custom "Others" values
            DB::statement("ALTER TABLE amenities MODIFY COLUMN type VARCHAR(255) NOT NULL DEFAULT 'Others'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite already uses string type, no change needed
            return;
        } else {
            // Convert back to ENUM (only standard values will be kept)
            // Convert 'others-*' values back to 'Others'
            DB::statement("UPDATE amenities SET type = 'Others' WHERE type LIKE 'others-%'");
            
            DB::statement("ALTER TABLE amenities MODIFY COLUMN type ENUM('Basketball', 'Pickleball', 'Badminton', 'Function Hall', 'Swimming Pool', 'Others') DEFAULT 'Others'");
        }
    }
};
