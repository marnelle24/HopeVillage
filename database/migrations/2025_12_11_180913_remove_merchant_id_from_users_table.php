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
        
        // Migrate existing merchant_id relationships to pivot table
        $users = DB::table('users')
            ->whereNotNull('merchant_id')
            ->where('user_type', 'merchant_user')
            ->get();

        foreach ($users as $user) {
            // Check if relationship already exists
            $exists = DB::table('merchant_user')
                ->where('merchant_id', $user->merchant_id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$exists) {
                // Set first merchant as default
                $hasDefault = DB::table('merchant_user')
                    ->where('user_id', $user->id)
                    ->where('is_default', true)
                    ->exists();

                DB::table('merchant_user')->insert([
                    'merchant_id' => $user->merchant_id,
                    'user_id' => $user->id,
                    'is_default' => !$hasDefault, // First one becomes default
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Remove merchant_id column
        if ($driver === 'sqlite') {
            // SQLite doesn't support dropping columns with foreign keys directly
            // We need to recreate the table without the column
            DB::statement('PRAGMA foreign_keys=off;');
            
            // Create new table without merchant_id
            Schema::create('users_new', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->timestamp('two_factor_confirmed_at')->nullable();
                $table->rememberToken();
                $table->foreignId('current_team_id')->nullable();
                $table->string('profile_photo_path', 2048)->nullable();
                $table->string('whatsapp_number')->nullable()->unique();
                $table->string('user_type')->default('member');
                $table->string('fin')->nullable()->unique();
                $table->string('qr_code')->nullable()->unique();
                $table->boolean('is_verified')->default(false);
                $table->integer('total_points')->default(0);
                $table->timestamps();
            });

            // Copy data from old table to new table (excluding merchant_id)
            // Explicitly list all columns that should be copied (excluding merchant_id)
            $columnList = 'id, name, email, email_verified_at, password, two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at, remember_token, current_team_id, profile_photo_path, whatsapp_number, user_type, fin, qr_code, is_verified, total_points, created_at, updated_at';
            
            DB::statement("
                INSERT INTO users_new ({$columnList})
                SELECT {$columnList}
                FROM users
            ");

            // Drop old table
            Schema::dropIfExists('users');

            // Rename new table
            Schema::rename('users_new', 'users');

            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            // For MySQL/PostgreSQL, use standard drop column
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['merchant_id']);
                $table->dropColumn('merchant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        // Add merchant_id column back
        Schema::table('users', function (Blueprint $table) use ($driver) {
            if ($driver === 'sqlite') {
                $table->foreignId('merchant_id')->nullable()->constrained()->onDelete('cascade');
            } else {
                $table->foreignId('merchant_id')->nullable()->after('user_type')->constrained()->onDelete('cascade');
            }
        });

        // Migrate default merchant back to merchant_id
        $merchantUsers = DB::table('merchant_user')
            ->where('is_default', true)
            ->get();

        foreach ($merchantUsers as $merchantUser) {
            DB::table('users')
                ->where('id', $merchantUser->user_id)
                ->update(['merchant_id' => $merchantUser->merchant_id]);
        }
    }
};
