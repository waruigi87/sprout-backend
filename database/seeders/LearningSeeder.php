<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ToDoItem;
use App\Models\Quiz;
use App\Models\SchoolClass;

class LearningSeeder extends Seeder
{
    public function run(): void
    {
        // 1. クラスID:1 に対してToDoを作成
        // ※ 既にクラスが存在している前提です
        $class = SchoolClass::first(); 
        if ($class) {
            ToDoItem::create([
                'class_id' => $class->id,
                'content' => 'タンクの水量を確認する',
                'is_completed' => false,
            ]);
            ToDoItem::create([
                'class_id' => $class->id,
                'content' => '枯れた葉を取り除く',
                'is_completed' => false,
            ]);
        }

        // 2. クイズを作成
        Quiz::create([
            'category' => '植物の基礎',
            'question' => '植物が光を使って栄養を作る働きを何という？',
            'options' => ['呼吸', '光合成', '蒸散'],
            'correct_index' => 1, // 光合成 (配列の1番目)
            'explanation' => '植物は太陽の光エネルギーを使って、二酸化炭素と水から栄養（デンプン）と酸素を作ります。これを光合成といいます。',
        ]);

        Quiz::create([
            'category' => '水耕栽培',
            'question' => '水耕栽培で一番大切な管理は？',
            'options' => ['毎日話しかける', '肥料の濃度管理', '土の入れ替え'],
            'correct_index' => 1,
            'explanation' => '水耕栽培では土を使わないため、水に溶けた肥料（養液）の濃度が植物の成長に直接影響します。',
        ]);
    }
}