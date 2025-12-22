<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SchoolClass;
use App\Models\Badge;
use App\Models\QuizAnswer;
use Illuminate\Support\Facades\DB;

class CheckBadges extends Command
{
    protected $signature = 'badges:check';
    protected $description = 'クラスのバッジ獲得条件をチェックして付与します';

    public function handle()
    {
        $this->info('Checking badges...');

        $classes = SchoolClass::all();
        $badges = Badge::all();

        foreach ($classes as $class) {
            foreach ($badges as $badge) {
                // 既に持っているバッジはスキップ
                // (リレーション経由でチェック)
                $hasBadge = DB::table('class_badges')
                    ->where('class_id', $class->id)
                    ->where('badge_id', $badge->id)
                    ->exists();

                if ($hasBadge) continue;

                // 条件判定
                if ($this->checkCondition($class, $badge)) {
                    // バッジ付与
                    DB::table('class_badges')->insert([
                        'class_id' => $class->id,
                        'badge_id' => $badge->id,
                        'awarded_at' => now(),
                    ]);
                    $this->info("Awarded '{$badge->name}' to Class {$class->name}");
                }
            }
        }

        $this->info('Badge check completed!');
    }

    // 条件判定ロジック
    private function checkCondition($class, $badge)
    {
        switch ($badge->condition_type) {
            case 'quiz_correct_count':
                // 正解数をカウント
                $count = QuizAnswer::where('class_id', $class->id)
                    ->where('is_correct', true)
                    ->count();
                return $count >= $badge->condition_value;

            // 他の条件があればここに追加 (例: login_streak, harvest_count)
            
            default:
                return false;
        }
    }
}