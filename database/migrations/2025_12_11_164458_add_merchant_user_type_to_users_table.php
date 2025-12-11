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
        
        // Modify user_type enum to include 'merchant_user' (only for MySQL/MariaDB)
        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'member', 'merchant_user') DEFAULT 'member'");
        }
        // For SQLite, enum is stored as string so it will accept any value
        
        // Add merchant_id foreign key
        Schema::table('users', function (Blueprint $table) use ($driver) {
            if ($driver === 'sqlite') {
                $table->foreignId('merchant_id')->nullable()->constrained()->onDelete('cascade');
            } else {
                $table->foreignId('merchant_id')->nullable()->after('user_type')->constrained()->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        Schema::table('users', function (Blueprint $table) {
            // SQLite doesn't support dropping foreign keys the same way
            if ($driver !== 'sqlite') {
                $table->dropForeign(['merchant_id']);
            }
            $table->dropColumn('merchant_id');
        });
        
        // Revert user_type enum back to original (only for MySQL/MariaDB)
        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'member') DEFAULT 'member'");
        }
    }
};
