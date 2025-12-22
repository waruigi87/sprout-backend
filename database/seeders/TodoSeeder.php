<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 全てのクラスを取得
        $classes = DB::table('classes')->get();
        
        if ($classes->isEmpty()) {
            return;
        }

        $todos = [
            '水の量は減っていないか確認する',
            '葉っぱの色は元気か確認する',
            '肥料を追加する（週に1回）',
        ];

        // 2. 全てのクラスに対してToDoを作成
        foreach ($classes as $class) {
            foreach ($todos as $content) {
                DB::table('todo_items')->insert([
                    'class_id' => $class->id,
                    'content' => $content,
                    'is_completed' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}