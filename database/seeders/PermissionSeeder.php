<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'semesters',
            'courses',
            'course-details',
            'books',
            'exams',
            'questions',
            'center-hours',
            'offers',
            'payment-codes',
            'correct-answers',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }
    }
}
