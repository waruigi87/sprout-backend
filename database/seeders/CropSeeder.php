<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CropSeeder extends Seeder
{
    public function run(): void
    {
        $crops = [
            ['name' => 'リーフレタス'],
            ['name' => 'スイートバジル'],
            ['name' => 'ミニトマト'],
            ['name' => 'ミント'],
        ];

        foreach ($crops as $crop) {
            DB::table('crops')->insert(array_merge($crop, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}