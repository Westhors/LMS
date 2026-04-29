<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Stage;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
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

        Teacher::create([
            'name' => 'Teacher',
            'sub_domain' => 'teacher',
            'email' => 'teacher@teacher.com',
            'phone' => '123456789',
            'password' => Hash::make('12345678'),
        ]);
        Student::create([
            'name' => 'Student',
            'email' => 'student@student.com',
            'password' => Hash::make('12345678'),
            'code_parent' => 'P001',
            'phone_parent' => '987654321',
            'teacher_id' => 1,
            'stage_id' => 1,
        ]);
    }
}
