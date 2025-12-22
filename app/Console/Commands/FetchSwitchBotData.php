<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sensor;
use App\Models\Reading;
use App\Services\SwitchBotService;
use Illuminate\Support\Facades\Log;

class FetchSwitchBotData extends Command
{
    /**
     * コマンド名（手動実行用: php artisan sensors:fetch）
     */
    protected $signature = 'sensors:fetch';

    protected $description = 'SwitchBotからセンサーデータを取得してDBに保存します';

    public function handle(SwitchBotService $switchBotService)
    {
        $this->info('Starting to fetch sensor data...');

        // 登録されているSwitchBot温湿度計を取得
        $sensors = Sensor::where('type', 'meter')->get();

        foreach ($sensors as $sensor) {
            try {
                $this->info("Fetching data for: {$sensor->name} ({$sensor->device_id})");
                
                // API呼び出し
                $data = $switchBotService->getDeviceStatus($sensor->device_id);
                $body = $data['body'] ?? [];

                // データが取得できているか確認
                if (isset($body['temperature']) && isset($body['humidity'])) {
                    
                    // 1. 温度の保存
                    Reading::create([
                        'sensor_id' => $sensor->id,
                        'type' => 'temperature',
                        'value' => $body['temperature'],
                        'recorded_at' => now(),
                    ]);

                    // 2. 湿度の保存
                    Reading::create([
                        'sensor_id' => $sensor->id,
                        'type' => 'humidity',
                        'value' => $body['humidity'],
                        'recorded_at' => now(),
                    ]);

                    $this->info("Saved: Temp {$body['temperature']}°C / Hum {$body['humidity']}%");
                } else {
                    $this->warn("No data found in response for {$sensor->device_id}");
                }

            } catch (\Exception $e) {
                // エラー時はログに出して処理を止めない
                Log::error("Failed to fetch sensor data: " . $e->getMessage());
                $this->error("Error: " . $e->getMessage());
            }

            // レートリミット回避のため少し待機（任意）
            sleep(1);
        }

        $this->info('All done!');
    }
}