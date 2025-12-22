<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HydroBedSeeder extends Seeder
{
    public function run(): void
    {
        $classA = DB::table('classes')->where('name', '3年A組')->first();
        $lettuce = DB::table('crops')->where('name', 'リーフレタス')->first();

        // 3年A組: 稼働中(Active)のベッド
        DB::table('hydro_beds')->insert([
            'class_id' => $classA->id,
            'crop_id' => $lettuce->id,
            'name' => '窓際1号機',
            'location' => '教室南側',
            'status' => 'active',
            'planted_at' => Carbon::now()->subDays(10), // 10日前に開始
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3年A組: 準備中(Standby)のベッド
        DB::table('hydro_beds')->insert([
            'class_id' => $classA->id,
            'crop_id' => null,
            'name' => '廊下側2号機',
            'location' => 'ロッカー上',
            'status' => 'standby',
            'planted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}