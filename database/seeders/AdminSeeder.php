<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $school = DB::table('schools')->first();

        DB::table('admins')->insert([
            'school_id' => $school->id,
            'name' => 'ICT担当者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // ログイン用パスワード
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}