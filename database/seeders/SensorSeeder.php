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

            DB::table('readings')->insert([
                'sensor_id' => $sensorId,
                'value' => $temp, // 本来はtypeで分けるが、簡易的に温度を入れる(仕様要確認: 通常はカラムを分けるか行を分ける)
                // ※今回の設計書では readings テーブルに type カラムがなく value だけなので
                // 厳密には温度用センサーID、湿度用センサーIDと分けるか、
                // readingsに typeカラムを持たせる必要があります。
                // ここでは便宜上「温度」として登録します。
                'recorded_at' => $targetTime,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 湿度用データ（今回は同じ時刻でレコードを分ける想定）
             DB::table('readings')->insert([
                'sensor_id' => $sensorId,
                'value' => $humidity,
                'recorded_at' => $targetTime,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}