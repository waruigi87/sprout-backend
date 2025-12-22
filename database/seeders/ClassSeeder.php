<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $school = DB::table('schools')->first();

        DB::table('classes')->insert([
            [
                'school_id' => $school->id,
                'name' => '3年A組',
                'code' => 'G3A2025',
                'locale' => 'ja',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => $school->id,
                'name' => '3年B組',
                'code' => 'G3B2025',
                'locale' => 'ja',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}