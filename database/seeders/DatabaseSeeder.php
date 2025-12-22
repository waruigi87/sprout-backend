<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SchoolSeeder::class,
            CropSeeder::class,
            BadgeSeeder::class,
            
            // ▼ 先にクラスを作らないと、ToDoを紐付けられません
            ClassSeeder::class,  // ← ここに移動！
            
            QuizSeeder::class,

            // ▼ クラスが存在した状態で実行する必要があります
            TodoSeeder::class,

            AdminSeeder::class,
            HydroBedSeeder::class,
            SensorSeeder::class,
        ]);
    }
}