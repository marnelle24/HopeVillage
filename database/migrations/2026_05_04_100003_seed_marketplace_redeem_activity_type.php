<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            [
                'name' => 'marketplace_redeem',
                'description' => 'Points spent on marketplace items',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'marketplace_refund',
                'description' => 'Points refunded from cancelled marketplace order',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($rows as $row) {
            if (! DB::table('activity_types')->where('name', $row['name'])->exists()) {
                DB::table('activity_types')->insert($row);
            }
        }
    }

    public function down(): void
    {
        DB::table('activity_types')->whereIn('name', ['marketplace_redeem', 'marketplace_refund'])->delete();
    }
};
