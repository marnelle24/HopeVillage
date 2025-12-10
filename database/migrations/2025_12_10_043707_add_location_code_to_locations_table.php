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
        $driver = DB::getDriverName();
        
        // Add location_code column
        if ($driver === 'sqlite') {
            Schema::table('locations', function (Blueprint $table) {
                $table->string('location_code', 12)->nullable()->unique();
            });
        } else {
            Schema::table('locations', function (Blueprint $table) {
                $table->string('location_code', 12)->unique()->after('id');
            });
        }

        // Generate location codes for existing locations
        $locations = DB::table('locations')->whereNull('location_code')->get();
        
        foreach ($locations as $location) {
            $code = $this->generateUniqueLocationCode();
            DB::table('locations')
                ->where('id', $location->id)
                ->update(['location_code' => $code]);
        }

        // Make it NOT NULL after populating (for SQLite, we'll keep it nullable or recreate)
        if ($driver !== 'sqlite') {
            Schema::table('locations', function (Blueprint $table) {
                $table->string('location_code', 12)->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropUnique(['location_code']);
            $table->dropColumn('location_code');
        });
    }

    /**
     * Generate a unique location code.
     */
    private function generateUniqueLocationCode(): string
    {
        do {
            $code = 'LOC-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (DB::table('locations')->where('location_code', $code)->exists());

        return $code;
    }
};
