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
            // SQLite doesn't support MODIFY COLUMN, so we need to recreate the table
            Schema::table('amenities', function (Blueprint $table) {
                $table->string('type_temp')->default('Others');
            });
            
            DB::statement("UPDATE amenities SET type_temp = type");
            
            Schema::table('amenities', function (Blueprint $table) {
                $table->dropColumn('type');
            });
            
            Schema::table('amenities', function (Blueprint $table) {
                $table->string('type')->default('Others');
            });
            
            DB::statement("UPDATE amenities SET type = type_temp");
            
            Schema::table('amenities', function (Blueprint $table) {
                $table->dropColumn('type_temp');
            });
        } else {
            // MySQL/MariaDB
            DB::statement("ALTER TABLE amenities MODIFY COLUMN type ENUM('Basketball', 'Pickleball', 'Badminton', 'Function Hall', 'Swimming Pool', 'Others') DEFAULT 'Others'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            Schema::table('amenities', function (Blueprint $table) {
                $table->string('type_temp')->default('facility');
            });
            
            DB::statement("UPDATE amenities SET type_temp = CASE WHEN type IN ('facility', 'function_hall') THEN type ELSE 'facility' END");
            
            Schema::table('amenities', function (Blueprint $table) {
                $table->dropColumn('type');
            });
            
            Schema::table('amenities', function (Blueprint $table) {
                $table->string('type')->default('facility');
            });
            
            DB::statement("UPDATE amenities SET type = type_temp");
            
            Schema::table('amenities', function (Blueprint $table) {
                $table->dropColumn('type_temp');
            });
        } else {
            DB::statement("ALTER TABLE amenities MODIFY COLUMN type ENUM('facility', 'function_hall') DEFAULT 'facility'");
        }
    }
};
