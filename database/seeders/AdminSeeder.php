<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Stage;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
        ]);

        Stage::create([
            'name' => 'Primary Stage',
            'position' => 1,
            'active' => true,
        ]);

        Subject::create([
            'name' => 'Arabic',
            'position' => 1,
            'stage_id' => 1,
            'active' => true,
        ]);
    }
}
