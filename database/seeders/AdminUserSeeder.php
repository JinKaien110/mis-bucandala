<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
       User::updateOrCreate(
            ['email' => 'admin@barangay.test'],
            [
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'registered_via' => 'admin',
            ]
        );

    }
}
