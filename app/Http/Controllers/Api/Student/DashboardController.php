<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\HydroBed;
use App\Models\Reading;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ToDoItem;

class DashboardController extends Controller
{
    /**
     * (4) ダッシュボード情報取得
     * GET /api/v1/classes/{id}/dashboard
     */
    public function index(Request $request, $id)
    {
        // クラスが存在するか確認
        $schoolClass = SchoolClass::findOrFail($id);

        // クラスに紐づくベッド一覧を取得
        // N+1問題を避けるため、センサーとその最新の計測データも一緒にロード
        $beds = HydroBed::where('class_id', $id)
            ->with(['crop', 'sensors.readings' => function ($query) {
                // 最新のデータだけを取得したいが、relationロード時はlimitが難しいケースがあるため
                // ここでは直近のデータを取得してコレクション側でフィルタリングします
                $query->latest('recorded_at')->limit(10);
            }])
            ->get();

        // レスポンス用のデータ整形
        $formattedBeds = $beds->map(function ($bed) {
            // センサーは1つと仮定（複数ある場合はループ処理が必要）
            $sensor = $bed->sensors->first();
            
            // 最新の温度・湿度を取得
            $tempData = $sensor?->readings->where('type', 'temperature')->first();
            $humData = $sensor?->readings->where('type', 'humidity')->first();

            return [
                'id' => $bed->id,
                'name' => $bed->name,
                'status' => $bed->status,
                'crop_name' => $bed->crop ? $bed->crop->name : null,
                // 栽培開始日からの経過日数
                'days_elapsed' => $bed->planted_at ? Carbon::parse($bed->planted_at)->diffInDays(now()) : null,
                'sensors' => [
                    'temperature' => [
                        'value' => $tempData ? $tempData->value : null,
                        'status' => $this->judgeStatus('temperature', $tempData?->value),
                    ],
                    'humidity' => [
                        'value' => $humData ? $humData->value : null,
                        'status' => $this->judgeStatus('humidity', $humData?->value),
                    ]
                ]
            ];
        });

        $todos = ToDoItem::where('class_id', $id)
        ->select('id', 'content', 'is_completed')
        ->orderBy('created_at', 'desc')
        ->get();



        // ToDoとバッジはまだテーブルがないため、ダミー（空配列）を返します
        return response()->json([
            'class_name' => $schoolClass->name,
            'beds' => $formattedBeds,
            'todos' => $todos,   
            'badges' => [],  
        ]);
    }

    /**
     * (5) グラフデータ取得
     * GET /api/v1/classes/{id}/graphs
     */
    public function graphs(Request $request, $id)
    {
        // クエリパラメータ ?range=7d などに対応
        $range = $request->input('range', '24h');
        
        // 取得範囲の決定
        $startDate = match($range) {
            '7d' => now()->subDays(7),
            default => now()->subHours(24),
        };

        // クラスにある最初のベッドのセンサーデータを取得
        // （仕様簡易化のため、クラス内の代表1つのセンサーを表示する想定）
        $bed = HydroBed::where('class_id', $id)->with('sensors')->first();

        if (!$bed || $bed->sensors->isEmpty()) {
            return response()->json(['range' => $range, 'data' => []]);
        }

        $sensorId = $bed->sensors->first()->id;

        // データを取得
        $readings = Reading::where('sensor_id', $sensorId)
            ->where('recorded_at', '>=', $startDate)
            ->orderBy('recorded_at', 'asc')
            ->get();

        // グラフ用にデータを整形 (同じ日時の温度と湿度をまとめる)
        // recorded_at をキーにしてグルーピング
        $grouped = $readings->groupBy(function ($item) {
            return $item->recorded_at->format('Y-m-d H:i:s');
        });

        $graphData = [];
        foreach ($grouped as $time => $items) {
            $temp = $items->where('type', 'temperature')->first();
            $hum = $items->where('type', 'humidity')->first();

            // 片方しかデータがない場合も考慮して追加
            if ($temp || $hum) {
                $graphData[] = [
                    'recorded_at' => $time,
                    'temperature' => $temp ? $temp->value : null,
                    'humidity' => $hum ? $hum->value : null,
                ];
            }
        }

        return response()->json([
            'range' => $range,
            'data' => $graphData
        ]);
    }

    /**
     * 値に基づいてステータス（good/warning/bad）を判定する内部メソッド
     */
    private function judgeStatus($type, $value)
    {
        if (is_null($value)) return 'bad';

        if ($type === 'temperature') {
            // 例: 15〜28℃なら適温
            if ($value >= 15 && $value <= 28) return 'good';
            // 極端に暑い/寒い
            if ($value < 10 || $value > 35) return 'bad';
            return 'warning';
        }

        if ($type === 'humidity') {
            // 例: 40〜80%なら適湿
            if ($value >= 40 && $value <= 80) return 'good';
            return 'warning';
        }

        return 'good';
    }
}