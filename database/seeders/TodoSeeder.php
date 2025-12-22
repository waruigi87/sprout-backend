<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 紐付け先のクラスを取得（とりあえず最初のクラス）
        $class = DB::table('classes')->first();
        
        // クラスがまだ作られていない場合は何もしない
        if (!$class) {
            return;
        }

        // 2. ToDoアイテムを登録
        $todos = [
            '水の量は減っていないか確認する',
            '葉っぱの色は元気か確認する',
            '肥料を追加する（週に1回）',
        ];

        foreach ($todos as $content) {
            DB::table('todo_items')->insert([
                // × 修正前: 'todo_template_id' => 1,
                // ○ 修正後: クラスIDを指定し、完了フラグもセット
                'class_id' => $class->id,
                'content' => $content,
                'is_completed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}