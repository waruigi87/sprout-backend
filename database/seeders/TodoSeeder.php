<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        // テンプレート作成
        $templateId = DB::table('todo_templates')->insertGetId([
            'title' => '毎日の観察ルーチン',
            'description' => '朝の会で確認しましょう',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 項目作成
        $items = [
            '水の量は減っていないか確認する',
            '葉っぱの色は緑色か確認する',
            'LEDライトが点灯しているか確認する',
        ];

        foreach ($items as $item) {
            DB::table('todo_items')->insert([
                'todo_template_id' => $templateId,
                'content' => $item,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}