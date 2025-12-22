<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('schools')->insert([
            'name' => '大阪インターナショナルスクール',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}