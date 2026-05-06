<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('member_activities', function (Blueprint $table) {
            $table->unique(
                ['user_id', 'activity_type_id', 'event_id'],
                'member_activities_user_activity_event_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_activities', function (Blueprint $table) {
            $table->dropUnique('member_activities_user_activity_event_unique');
        });
    }
};
