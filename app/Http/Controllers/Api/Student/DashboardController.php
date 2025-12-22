<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\HydroBed;
use App\Models\Reading;
use App\Models\ToDoItem;
use App\Models\Badge; // ★追加
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request, $id)
    {
        // 1. クラスを取得
        $schoolClass = SchoolClass::findOrFail($id);

        // 2. ベッド情報の取得（変更なし）
        $beds = HydroBed::where('class_id', $id)
            ->with(['crop', 'sensors.readings' => function ($query) {
                $query->latest('recorded_at')->limit(10);
            }])
            ->get();

        $formattedBeds = $beds->map(function ($bed) {
            $sensor = $bed->sensors->first();
            $tempData = $sensor?->readings->where('type', 'temperature')->first();
            $humData = $sensor?->readings->where('type', 'humidity')->first();

            return [
                'id' => $bed->id,
                'name' => $bed->name,
                'status' => $bed->status,
                'crop_name' => $bed->crop ? $bed->crop->name : null,
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

        // 3. ToDoリストの取得（変更なし）
        $todos = ToDoItem::where('class_id', $id)
            ->select('id', 'content', 'is_completed')
            ->orderBy('created_at', 'desc')
            ->get();

        // 4. ▼▼▼ バッジ情報の取得ロジック（ここを追加・修正） ▼▼▼
        
        // 全種類のバッジを取得（マスタデータ）
        $allBadges = Badge::all();
        
        // このクラスが既に獲得しているバッジのIDリストを取得
        // リレーション経由で取得し、IDの配列にする
        $acquiredBadgeIds = $schoolClass->badges()->pluck('badges.id')->toArray();

        // 全バッジをループし、獲得済みかどうか(acquired)を判定して整形
        $formattedBadges = $allBadges->map(function ($badge) use ($acquiredBadgeIds) {
            return [
                'id' => $badge->id,
                'name' => $badge->name,
                
                // 画像パスの生成 (public/images/badges/ に画像がある想定)
                // image_key があればパスを生成、なければ null
                'image_url' => $badge->image_key ? "/images/badges/{$badge->image_key}" : null,
                
                // 獲得済みなら true, 未獲得なら false
                'acquired' => in_array($badge->id, $acquiredBadgeIds),
            ];
        });

        return response()->json([
            'class_name' => $schoolClass->name,
            'beds' => $formattedBeds,
            'todos' => $todos,   
            'badges' => $formattedBadges, // 整形したデータを渡す
        ]);
    }

    // ... (graphsメソッドやjudgeStatusメソッドは変更なし) ...
    public function graphs(Request $request, $id) { /* ... */ }
    private function judgeStatus($type, $value) { /* ... */ }
}