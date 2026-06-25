<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminsSeeder extends Seeder
{
    public function run(): void
    {
        Teacher::create([
            'name' => 'Super Admin LMS',
            'email' => 'Eslam@superadmin0716.com',
            'password' => Hash::make('0123456789eslaM#%*$'),
        ]);
    }
}

