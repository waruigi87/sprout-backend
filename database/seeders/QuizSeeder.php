<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('quizzes')->insert([
            [
                'category' => '光合成',
                'question' => '植物が光合成をするために必要なものは、光と水とあと一つは何？',
                'options' => json_encode(['酸素', '二酸化炭素', '窒素']),
                'answer_index' => 1, // 二酸化炭素
                'explanation' => '植物は光のエネルギーを使って、二酸化炭素と水からデンプンなどの養分を作ります。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => '水耕栽培',
                'question' => '水耕栽培で土の代わりに使うものは？',
                'options' => json_encode(['水と肥料', '砂', '粘土']),
                'answer_index' => 0,
                'explanation' => '水耕栽培では、土を使わず水に肥料（養液）を溶かして育てます。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => '植物のつくり',
                'question' => '根から吸い上げた水が通る管を何という？',
                'options' => json_encode(['師管', '道管', '血管']),
                'answer_index' => 1,
                'explanation' => '根から吸い上げた水や養分は「道管」を通って全体に運ばれます。',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}