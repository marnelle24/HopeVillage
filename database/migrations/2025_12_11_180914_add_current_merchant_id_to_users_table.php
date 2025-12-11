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
        
        // Check if column already exists (might have been created without foreign key)
        if (!Schema::hasColumn('users', 'current_merchant_id')) {
            Schema::table('users', function (Blueprint $table) use ($driver) {
                if ($driver === 'sqlite') {
                    $table->foreignId('current_merchant_id')->nullable()->constrained('merchants')->onDelete('set null');
                } else {
                    $table->foreignId('current_merchant_id')->nullable()->after('user_type')->constrained('merchants')->onDelete('set null');
                }
            });
        } else {
            // Column exists, but we need to ensure the foreign key exists
            // Check if foreign key constraint already exists
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'users' 
                AND COLUMN_NAME = 'current_merchant_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (empty($foreignKeys)) {
                // Add foreign key constraint
                DB::statement("
                    ALTER TABLE users 
                    ADD CONSTRAINT users_current_merchant_id_foreign 
                    FOREIGN KEY (current_merchant_id) REFERENCES merchants(id) 
                    ON DELETE SET NULL
                ");
            }
        }

        // Set current_merchant_id to default merchant for existing merchant users
        $merchantUsers = DB::table('merchant_user')
            ->where('is_default', true)
            ->get();

        foreach ($merchantUsers as $merchantUser) {
            DB::table('users')
                ->where('id', $merchantUser->user_id)
                ->update(['current_merchant_id' => $merchantUser->merchant_id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $driver = DB::connection()->getDriverName();
            if ($driver !== 'sqlite') {
                $table->dropForeign(['current_merchant_id']);
            }
            $table->dropColumn('current_merchant_id');
        });
    }
};
