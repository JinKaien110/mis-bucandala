<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Disabled - admins table doesn't exist, using barangay_officials instead
        // $user = User::updateOrCreate(
        //     ['email' => 'admin@barangay.test'],
        //     [
        //         'password' => Hash::make('password123'),
        //         'role' => 'admin',
        //         'status' => 'active',
        //     ]
        // );

        // Admin::updateOrCreate(
        //     ['user_id' => $user->id],
        //     [
        //         'first_name' => 'Sherlyn',
        //         'last_name' => 'Quider',
        //         'role' => 'admin',
        //         'position' => 'Barangay Captain',
        //         'status' => 'active',
        //         'phone' => '091235467890',
        //         'timestamp' => now(),
        //     ]
        // );
    }
}
