<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        // クイズ系バッジ
        Badge::create([
            'name' => 'クイズ初心者',
            'description' => 'クイズに1回正解する',
            'image_key' => 'quiz_bronze',
            'condition_type' => 'quiz_correct_count',
            'condition_value' => 1,
        ]);
        
        Badge::create([
            'name' => '物知り博士',
            'description' => 'クイズに5回正解する',
            'image_key' => 'quiz_gold',
            'condition_type' => 'quiz_correct_count',
            'condition_value' => 5,
        ]);
    }
}