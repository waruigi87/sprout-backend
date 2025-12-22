<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. マスタ系
            SchoolSeeder::class,
            CropSeeder::class,
            BadgeSeeder::class,
            QuizSeeder::class,
            TodoSeeder::class,

            // 2. 学校依存系
            AdminSeeder::class,
            ClassSeeder::class,

            // 3. 運用データ系
            HydroBedSeeder::class,
            SensorSeeder::class,
        ]);
    }
}