<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SensorSeeder extends Seeder
{
    public function run(): void
    {
        // Activeなベッドを取得
        $bed = DB::table('hydro_beds')->where('status', 'active')->first();
        if (!$bed) return;

        // センサー登録 (SwitchBot Meterを想定)
        $sensorId = DB::table('sensors')->insertGetId([
            'hydro_bed_id' => $bed->id,
            'device_id' => 'C0:34:56:78:90:AB', // ダミーMACアドレス
            'type' => 'meter', // 温湿度計
            'name' => 'SwitchBot Meter',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 過去24時間分のダミー計測データを作成 (1時間おき)
        $now = Carbon::now();
        for ($i = 24; $i >= 0; $i--) {
            $targetTime = $now->copy()->subHours($i);
            
            // 少しランダムな値にする (20~25度, 40~60%)
            $temp = 22.0 + (rand(-20, 30) / 10); 
            $humidity = 50.0 + (rand(-100, 100) / 10);

            // ▼▼▼ 修正箇所: type => 'temperature' を追加 ▼▼▼
            DB::table('readings')->insert([
                'sensor_id' => $sensorId,
                'value' => $temp,
                'type' => 'temperature', // ★ここを追加しました
                'recorded_at' => $targetTime,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ▼▼▼ 修正箇所: type => 'humidity' を追加 ▼▼▼
             DB::table('readings')->insert([
                'sensor_id' => $sensorId,
                'value' => $humidity,
                'type' => 'humidity',   // ★ここを追加しました
                'recorded_at' => $targetTime,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}