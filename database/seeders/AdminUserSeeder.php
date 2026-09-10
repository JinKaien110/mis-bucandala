<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
       $user = User::updateOrCreate(
            ['email' => 'admin@barangay.test'],
            [
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'registered_via' => 'admin',
            ]
        );

        Admin::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => 'Sherlyn',
                'last_name' => 'Quider',
                'position' => 'Barangay Captain',
                'timestamp' => now(),
            ]
        );
    }
}
