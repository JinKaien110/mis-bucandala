<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\BarangayOfficial;
use App\Models\BarangayTerm;
use App\Models\Blotter;
use App\Models\CaseFile;
use App\Models\CaseHearing;
use App\Models\Event;
use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\Pet;
use App\Models\Resident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $adminUser = User::updateOrCreate(
                ['email' => 'admin@barangay.test'],
                [
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'status' => 'active',
                    'registered_via' => 'admin',
                ]
            );

            Admin::updateOrCreate(
                ['user_id' => $adminUser->id],
                [
                    'first_name' => 'Sherlyn',
                    'last_name' => 'Quider',
                    'position' => 'Barangay Captain',
                    'timestamp' => now(),
                ]
            );

            $staffUser = User::updateOrCreate(
                ['email' => 'staff@barangay.test'],
                [
                    'password' => Hash::make('password123'),
                    'role' => 'staff',
                    'status' => 'active',
                    'registered_via' => 'admin',
                ]
            );

            Admin::updateOrCreate(
                ['user_id' => $staffUser->id],
                [
                    'first_name' => 'Maria',
                    'last_name' => 'Santos',
                    'position' => 'Barangay Secretary',
                    'timestamp' => now(),
                ]
            );

            $additionalStaff = [
                [
                    'email' => 'treasurer@barangay.test',
                    'first_name' => 'Arnel',
                    'last_name' => 'Santos',
                    'position' => 'Barangay Treasurer',
                    'role' => 'staff'
                ],
                [
                    'email' => 'clerk@barangay.test',
                    'first_name' => 'Karen',
                    'last_name' => 'Lopez',
                    'position' => 'Barangay Clerk',
                    'role' => 'clerk'
                ],
                [
                    'email' => 'lupon1@barangay.test',
                    'first_name' => 'Ricardo',
                    'last_name' => 'Velasco',
                    'position' => 'Lupon Member',
                    'role' => 'blotter'
                ],
                [
                    'email' => 'lupon2@barangay.test',
                    'first_name' => 'Diana',
                    'last_name' => 'Davis',
                    'position' => 'Lupon Member',
                    'role' => 'blotter'
                ],
                [
                    'email' => 'lupon3@barangay.test',
                    'first_name' => 'Robert',
                    'last_name' => 'Castillo',
                    'position' => 'Lupon Member',
                    'role' => 'blotter'
                ],
            ];

            foreach ($additionalStaff as $staff) {
                $u = User::updateOrCreate(
                    ['email' => $staff['email']],
                    [
                        'password' => Hash::make('password123'),
                        'role' => $staff['role'],
                        'status' => 'active',
                        'registered_via' => 'admin',
                    ]
                );

                Admin::updateOrCreate(
                    ['user_id' => $u->id],
                    [
                        'first_name' => $staff['first_name'],
                        'last_name' => $staff['last_name'],
                        'position' => $staff['position'],
                        'timestamp' => now(),
                    ]
                );
            }

            $residentsData = [
                [
                    'email' => 'juan.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Juan',
                    'middle_name' => 'S.',
                    'last_name' => 'Dela Cruz',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(32)->format('Y-m-d'),
                    'contact_no' => '09181234567',
                    'address_line' => '123 Mabini Street',
                    'civil_status' => 'single',
                    'occupation' => 'Technician',
                ],
                [
                    'email' => 'maria.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Maria',
                    'middle_name' => 'L.',
                    'last_name' => 'Santos',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(28)->format('Y-m-d'),
                    'contact_no' => '09182345678',
                    'address_line' => '456 Mabini Street',
                    'civil_status' => 'married',
                    'occupation' => 'Teacher',
                ],
                [
                    'email' => 'pedro.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Pedro',
                    'middle_name' => 'A.',
                    'last_name' => 'Reyes',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(40)->format('Y-m-d'),
                    'contact_no' => '09183456789',
                    'address_line' => '789 Mabini Street',
                    'civil_status' => 'married',
                    'occupation' => 'Businessman',
                ],
                [
                    'email' => 'amelia.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Amelia',
                    'middle_name' => 'E.',
                    'last_name' => 'Gonzales',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(35)->format('Y-m-d'),
                    'contact_no' => '09184567890',
                    'address_line' => '101 Poblacion Road',
                    'civil_status' => 'married',
                    'occupation' => 'Nurse',
                ],
                [
                    'email' => 'rico.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Rico',
                    'middle_name' => 'M.',
                    'last_name' => 'Velasco',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(26)->format('Y-m-d'),
                    'contact_no' => '09185678901',
                    'address_line' => '202 Rizal Avenue',
                    'civil_status' => 'single',
                    'occupation' => 'Delivery Rider',
                ],
                [
                    'email' => 'ina.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Ina',
                    'middle_name' => 'P.',
                    'last_name' => 'Garcia',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(22)->format('Y-m-d'),
                    'contact_no' => '09186789012',
                    'address_line' => '303 Bonifacio Drive',
                    'civil_status' => 'single',
                    'occupation' => 'Student',
                ],
                [
                    'email' => 'cesar.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Cesar',
                    'middle_name' => 'C.',
                    'last_name' => 'Fernandez',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(50)->format('Y-m-d'),
                    'contact_no' => '09187890123',
                    'address_line' => '404 Mabini Street',
                    'civil_status' => 'married',
                    'occupation' => 'Retired',
                ],
                [
                    'email' => 'althea.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Althea',
                    'middle_name' => 'G.',
                    'last_name' => 'Torres',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(30)->format('Y-m-d'),
                    'contact_no' => '09188901234',
                    'address_line' => '505 Rizal Avenue',
                    'civil_status' => 'single',
                    'occupation' => 'Cashier',
                ],
                [
                    'email' => 'rain.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Rain',
                    'middle_name' => 'J.',
                    'last_name' => 'Domingo',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(27)->format('Y-m-d'),
                    'contact_no' => '09189012345',
                    'address_line' => '606 Mabini Street',
                    'civil_status' => 'single',
                    'occupation' => 'Photographer',
                ],
                [
                    'email' => 'karen.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Karen',
                    'middle_name' => 'D.',
                    'last_name' => 'Lopez',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(29)->format('Y-m-d'),
                    'contact_no' => '09190123456',
                    'address_line' => '707 Aguinaldo Highway',
                    'civil_status' => 'single',
                    'occupation' => 'Office Clerk',
                ],
                [
                    'email' => 'jose.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Jose',
                    'middle_name' => 'M.',
                    'last_name' => 'Ramos',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(33)->format('Y-m-d'),
                    'contact_no' => '09191234567',
                    'address_line' => '808 Bonifacio Drive',
                    'civil_status' => 'married',
                    'occupation' => 'Driver',
                ],
                [
                    'email' => 'anna.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Anna',
                    'middle_name' => 'C.',
                    'last_name' => 'Lim',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(24)->format('Y-m-d'),
                    'contact_no' => '09192345678',
                    'address_line' => '909 Dela Rosa Street',
                    'civil_status' => 'single',
                    'occupation' => 'Sales Associate',
                ],
                [
                    'email' => 'miguel.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Miguel',
                    'middle_name' => 'D.',
                    'last_name' => 'Tan',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(21)->format('Y-m-d'),
                    'contact_no' => '09193456789',
                    'address_line' => '111 Lakandula Street',
                    'civil_status' => 'single',
                    'occupation' => 'College Student',
                ],
                [
                    'email' => 'sofia.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Sofia',
                    'middle_name' => 'R.',
                    'last_name' => 'Diaz',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(31)->format('Y-m-d'),
                    'contact_no' => '09194567890',
                    'address_line' => '222 Roxas Boulevard',
                    'civil_status' => 'married',
                    'occupation' => 'Accountant',
                ],
                [
                    'email' => 'carlo.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Carlo',
                    'middle_name' => 'G.',
                    'last_name' => 'Navarro',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(45)->format('Y-m-d'),
                    'contact_no' => '09195678901',
                    'address_line' => '333 Taft Avenue',
                    'civil_status' => 'married',
                    'occupation' => 'Mechanic',
                ],
                [
                    'email' => 'liza.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Liza',
                    'middle_name' => 'P.',
                    'last_name' => 'Ortega',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(25)->format('Y-m-d'),
                    'contact_no' => '09196789012',
                    'address_line' => '444 Quezon Boulevard',
                    'civil_status' => 'single',
                    'occupation' => 'Barista',
                ],
                [
                    'email' => 'ben.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Ben',
                    'middle_name' => 'T.',
                    'last_name' => 'Aguilar',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(38)->format('Y-m-d'),
                    'contact_no' => '09197890123',
                    'address_line' => '555 Padre Faura Street',
                    'civil_status' => 'married',
                    'occupation' => 'Security Guard',
                ],
                [
                    'email' => 'mia.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Mia',
                    'middle_name' => 'S.',
                    'last_name' => 'Herrera',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(23)->format('Y-m-d'),
                    'contact_no' => '09198901234',
                    'address_line' => '666 San Marcelino Street',
                    'civil_status' => 'single',
                    'occupation' => 'Content Creator',
                ],
                [
                    'email' => 'dave.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Dave',
                    'middle_name' => 'L.',
                    'last_name' => 'Castro',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(36)->format('Y-m-d'),
                    'contact_no' => '09199012345',
                    'address_line' => '777 Adriatico Street',
                    'civil_status' => 'single',
                    'occupation' => 'Fitness Trainer',
                ],
                [
                    'email' => 'eva.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Eva',
                    'middle_name' => 'R.',
                    'last_name' => 'Santos',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(28)->format('Y-m-d'),
                    'contact_no' => '09199012346',
                    'address_line' => '888 Roxas Boulevard',
                    'civil_status' => 'married',
                    'occupation' => 'Accountant',
                ],
                [
                    'email' => 'robert.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Robert',
                    'middle_name' => 'V.',
                    'last_name' => 'Castillo',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(41)->format('Y-m-d'),
                    'contact_no' => '09200123456',
                    'address_line' => '999 Retiro Street',
                    'civil_status' => 'married',
                    'occupation' => 'Consultant',
                ],
                [
                    'email' => 'fiona.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Fiona',
                    'middle_name' => 'B.',
                    'last_name' => 'Summers',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(26)->format('Y-m-d'),
                    'contact_no' => '09201234567',
                    'address_line' => '100 Mahabang Parang',
                    'civil_status' => 'single',
                    'occupation' => 'Teacher',
                ],
                [
                    'email' => 'lucas.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Lucas',
                    'middle_name' => 'H.',
                    'last_name' => 'Santos',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(34)->format('Y-m-d'),
                    'contact_no' => '09202345678',
                    'address_line' => '101 Alabang Street',
                    'civil_status' => 'married',
                    'occupation' => 'Engineer',
                ],
                [
                    'email' => 'claire.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Claire',
                    'middle_name' => 'N.',
                    'last_name' => 'Martinez',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(19)->format('Y-m-d'),
                    'contact_no' => '09203456789',
                    'address_line' => '102 Kalikasan Street',
                    'civil_status' => 'single',
                    'occupation' => 'College Student',
                ],
                [
                    'email' => 'david.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'David',
                    'middle_name' => 'J.',
                    'last_name' => 'Wilson',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(37)->format('Y-m-d'),
                    'contact_no' => '09204567890',
                    'address_line' => '103 Inya ng Laya',
                    'civil_status' => 'married',
                    'occupation' => 'Businessman',
                ],
                [
                    'email' => 'grace.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Grace',
                    'middle_name' => 'O.',
                    'last_name' => 'Johnson',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(43)->format('Y-m-d'),
                    'contact_no' => '09205678901',
                    'address_line' => '104 Likhaan Street',
                    'civil_status' => 'married',
                    'occupation' => 'Administrator',
                ],
                [
                    'email' => 'mark.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Mark',
                    'middle_name' => 'K.',
                    'last_name' => 'Brown',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(25)->format('Y-m-d'),
                    'contact_no' => '09206789012',
                    'address_line' => '105 Lipat ng Bahay',
                    'civil_status' => 'single',
                    'occupation' => 'Developer',
                ],
                [
                    'email' => 'diana.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Diana',
                    'middle_name' => 'X.',
                    'last_name' => 'Davis',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(32)->format('Y-m-d'),
                    'contact_no' => '09207890123',
                    'address_line' => '106 Liwayway Avenue',
                    'civil_status' => 'married',
                    'occupation' => 'Manager',
                ],
                [
                    'email' => 'oliver.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Oliver',
                    'middle_name' => 'Z.',
                    'last_name' => 'Garcia',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(20)->format('Y-m-d'),
                    'contact_no' => '09208901234',
                    'address_line' => '107 Lopende Bahay',
                    'civil_status' => 'single',
                    'occupation' => 'Student',
                ],
                [
                    'email' => 'sophia.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Sophia',
                    'middle_name' => 'U.',
                    'last_name' => 'Rodriguez',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(27)->format('Y-m-d'),
                    'contact_no' => '09209012345',
                    'address_line' => '108 Lupang Hinirang',
                    'civil_status' => 'single',
                    'occupation' => 'Designer',
                ],
                [
                    'email' => 'james.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'James',
                    'middle_name' => 'Q.',
                    'last_name' => 'Thompson',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(39)->format('Y-m-d'),
                    'contact_no' => '09210123456',
                    'address_line' => '109 Mahigit Street',
                    'civil_status' => 'married',
                    'occupation' => 'Technician',
                ],
                [
                    'email' => 'rachel.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Rachel',
                    'middle_name' => 'W.',
                    'last_name' => 'Martinez',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(23)->format('Y-m-d'),
                    'contact_no' => '09211234567',
                    'address_line' => '110 Mahiwagang Ayala',
                    'civil_status' => 'single',
                    'occupation' => 'Analyst',
                ],
                [
                    'email' => 'christopher.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Christopher',
                    'middle_name' => 'Y.',
                    'last_name' => 'Anderson',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(44)->format('Y-m-d'),
                    'contact_no' => '09212345678',
                    'address_line' => '111 Mahusay Street',
                    'civil_status' => 'married',
                    'occupation' => 'Director',
                ],
                [
                    'email' => 'emma.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Emma',
                    'middle_name' => 'L.',
                    'last_name' => 'Taylor',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(18)->format('Y-m-d'),
                    'contact_no' => '09213456789',
                    'address_line' => '112 Makabago Street',
                    'civil_status' => 'single',
                    'occupation' => 'Intern',
                ],
                [
                    'email' => 'ryan.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Ryan',
                    'middle_name' => 'F.',
                    'last_name' => 'Thomas',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(28)->format('Y-m-d'),
                    'contact_no' => '09214567890',
                    'address_line' => '113 Makabuluhan Street',
                    'civil_status' => 'single',
                    'occupation' => 'Artist',
                ],
                [
                    'email' => 'victoria.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Victoria',
                    'middle_name' => 'E.',
                    'last_name' => 'Jackson',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(35)->format('Y-m-d'),
                    'contact_no' => '09215678901',
                    'address_line' => '114 Makakalikasan Street',
                    'civil_status' => 'married',
                    'occupation' => 'Lawyer',
                ],
                [
                    'email' => 'andrew.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Andrew',
                    'middle_name' => 'M.',
                    'last_name' => 'White',
                    'sex' => 'male',
                    'birth_date' => now()->subYears(30)->format('Y-m-d'),
                    'contact_no' => '09216789012',
                    'address_line' => '115 Makakasama Street',
                    'civil_status' => 'married',
                    'occupation' => 'Doctor',
                ],
                [
                    'email' => 'michelle.resident@barangay.test',
                    'password' => 'password123',
                    'first_name' => 'Michelle',
                    'middle_name' => 'A.',
                    'last_name' => 'Harris',
                    'sex' => 'female',
                    'birth_date' => now()->subYears(29)->format('Y-m-d'),
                    'contact_no' => '09217890123',
                     'address_line' => '116 Makarakal Street',
                     'civil_status' => 'single',
                     'occupation' => 'Nurse',
                 ],
                 [
                     'email' => 'fernando.resident@barangay.test',
                     'password' => 'password123',
                     'first_name' => 'Fernando',
                     'middle_name' => 'R.',
                     'last_name' => 'Santos',
                     'sex' => 'male',
                     'birth_date' => now()->subYears(25)->format('Y-m-d'),
                     'contact_no' => '09218901234',
                     'address_line' => '117 New Street',
                     'civil_status' => 'single',
                     'occupation' => 'Worker',
                 ],
                 [
                     'email' => 'geralda.resident@barangay.test',
                     'password' => 'password123',
                     'first_name' => 'Geralda',
                     'middle_name' => 'M.',
                     'last_name' => 'Lopez',
                     'sex' => 'female',
                     'birth_date' => now()->subYears(28)->format('Y-m-d'),
                     'contact_no' => '09219012345',
                     'address_line' => '118 Another Street',
                     'civil_status' => 'married',
                     'occupation' => 'Employee',
                 ],
             ];

            $residents = [];
            $residentEmails = [];
            foreach ($residentsData as $key => $data) {
                $user = User::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'password' => Hash::make($data['password']),
                        'role' => 'resident',
                        'status' => 'active',
                        'registered_via' => 'public_form',
                    ]
                );

                $educationalAttainments = ['No Formal Education', 'Elementary Graduate', 'High School Graduate', 'College Graduate', 'Vocational', 'Post Graduate'];
                $residentData = [
                    'first_name'              => $data['first_name'],
                    'middle_name'             => $data['middle_name'],
                    'last_name'               => $data['last_name'],
                    'sex'                     => $data['sex'],
                    'birth_date'              => $data['birth_date'],
                    'address_line'            => $data['address_line'],
                    'barangay'                => 'Bucandala 1',
                    'city'                    => 'Imus',
                    'province'                => 'Cavite',
                    'contact_no'              => $data['contact_no'],
                    'email'                   => $data['email'],
                    'civil_status'            => $data['civil_status'],
                    'occupation'              => $data['occupation'],
                    'verification_status'     => 'verified',
                    'registered_via'          => 'public_form',
                    'status'                  => 'active',
                    'user_id'                 => $user->id,
                    'account_no'              => Resident::generateAccountNo(),
                    'monthly_income'          => round(mt_rand(5000, 45000) + mt_rand(1, 99) / 100, 2),
                    'educational_attainment'  => $educationalAttainments[array_rand($educationalAttainments)],
                    'solo_parent'             => (int) (mt_rand(1, 10) === 1),
                    'pwd'                     => (int) (mt_rand(1, 20) === 1),
                    'indigent'                => (int) (mt_rand(1, 7) === 1),
                    'four_ps_beneficiary'     => (int) (mt_rand(1, 5) === 1),
                    'employment_status'       => in_array(strtolower($data['occupation'] ?? ''), ['student', 'unemployed']) ? 'unemployed' : 'employed',
                ];

                $resident = Resident::updateOrCreate(
                    ['email' => $data['email']],
                    $residentData
                );

                $residents[] = $resident;
                $residentEmails[] = $data['email'];
                $this->logAudit($staffUser, 'residents', 'created', $resident->id, $resident->toArray());
            }

            $householdData = [
                [
                    'household_code' => 'HH-2026-0001',
                    'address_line' => '123 Mabini Street',
                    'purok' => 'Purok 1',
                    'members' => [0, 1],
                    'relationships' => ['Head', 'Spouse'],
                ],
                [
                    'household_code' => 'HH-2026-0002',
                    'address_line' => '456 Mabini Street',
                    'purok' => 'Purok 1',
                    'members' => [2, 3, 4],
                    'relationships' => ['Head', 'Spouse', 'Child'],
                ],
                [
                    'household_code' => 'HH-2026-0003',
                    'address_line' => '789 Mabini Street',
                    'purok' => 'Purok 2',
                    'members' => [5, 6],
                    'relationships' => ['Head', 'Child'],
                ],
                [
                    'household_code' => 'HH-2026-0004',
                    'address_line' => '101 Poblacion Road',
                    'purok' => 'Purok 3',
                    'members' => [7, 8],
                    'relationships' => ['Head', 'Spouse'],
                ],
                [
                    'household_code' => 'HH-2026-0005',
                    'address_line' => '202 Rizal Avenue',
                    'purok' => 'Purok 4',
                    'members' => [9],
                    'relationships' => ['Head'],
                ],
                [
                    'household_code' => 'HH-2026-0006',
                    'address_line' => '303 Bonifacio Drive',
                    'purok' => 'Purok 4',
                    'members' => [10, 11, 12],
                    'relationships' => ['Head', 'Spouse', 'Child'],
                ],
                [
                    'household_code' => 'HH-2026-0007',
                    'address_line' => '505 Rizal Avenue',
                    'purok' => 'Purok 3',
                    'members' => [13, 14],
                    'relationships' => ['Head', 'Spouse'],
                ],
                [
                    'household_code' => 'HH-2026-0008',
                    'address_line' => '808 Bonifacio Drive',
                    'purok' => 'Purok 2',
                    'members' => [15, 16],
                    'relationships' => ['Head', 'Child'],
                ],
                [
                    'household_code' => 'HH-2026-0009',
                    'address_line' => '909 Dela Rosa Street',
                    'purok' => 'Purok 1',
                    'members' => [17, 18],
                    'relationships' => ['Head', 'Spouse'],
                ],
                [
                    'household_code' => 'HH-2026-0010',
                    'address_line' => '111 Lakandula Street',
                    'purok' => 'Purok 4',
                    'members' => [19],
                    'relationships' => ['Head'],
                ],
                [
                    'household_code' => 'HH-2026-0011',
                    'address_line' => '222 Roxas Boulevard',
                    'purok' => 'Purok 3',
                    'members' => [20, 21, 22],
                    'relationships' => ['Head', 'Spouse', 'Child'],
                ],
                [
                    'household_code' => 'HH-2026-0012',
                    'address_line' => '333 Taft Avenue',
                    'purok' => 'Purok 2',
                    'members' => [23, 24],
                    'relationships' => ['Head', 'Spouse'],
                ],
                [
                    'household_code' => 'HH-2026-0013',
                    'address_line' => '444 Quezon Boulevard',
                    'purok' => 'Purok 1',
                    'members' => [25],
                    'relationships' => ['Head'],
                ],
                [
                    'household_code' => 'HH-2026-0014',
                    'address_line' => '555 Padre Faura Street',
                    'purok' => 'Purok 4',
                    'members' => [26, 27, 28],
                    'relationships' => ['Head', 'Spouse', 'Child'],
                ],
                [
                    'household_code' => 'HH-2026-0015',
                    'address_line' => '666 San Marcelino Street',
                    'purok' => 'Purok 3',
                    'members' => [29],
                    'relationships' => ['Head'],
                ],
                [
                    'household_code' => 'HH-2026-0016',
                    'address_line' => '777 Adriatico Street',
                    'purok' => 'Purok 1',
                    'members' => [30, 31],
                    'relationships' => ['Head', 'Spouse'],
                ],
                [
                    'household_code' => 'HH-2026-0017',
                    'address_line' => '888 Remedios Circle',
                    'purok' => 'Purok 2',
                    'members' => [32, 33, 34],
                    'relationships' => ['Head', 'Spouse', 'Child'],
                ],
                [
                    'household_code' => 'HH-2026-0018',
                    'address_line' => '150 P. Gomez Street',
                    'purok' => 'Purok 4',
                    'members' => [35, 36],
                    'relationships' => ['Head', 'Child'],
                ],
                [
                    'household_code' => 'HH-2026-0019',
                    'address_line' => '200 J. Luna Street',
                    'purok' => 'Purok 1',
                    'members' => [37, 38],
                    'relationships' => ['Head', 'Spouse'],
                ],
                [
                    'household_code' => 'HH-2026-0020',
                    'address_line' => '350 A. Mabini Street',
                    'purok' => 'Purok 3',
                    'members' => [39],
                    'relationships' => ['Head'],
                ],
            ];

            foreach ($householdData as $householdInfo) {
                $household = Household::updateOrCreate(
                    ['household_code' => $householdInfo['household_code']],
                    [
                        'address_line' => $householdInfo['address_line'],
                        'purok' => $householdInfo['purok'],
                    ]
                );

                foreach ($householdInfo['members'] as $index => $residentIndex) {
                    $member = $residents[$residentIndex];
                    $relationship = $householdInfo['relationships'][$index] ?? 'Member';

                    if (Schema::hasColumn('residents', 'household_id')) {
                        $member->update(['household_id' => $household->id]);
                    }

                    HouseholdMember::updateOrCreate(
                        ['household_id' => $household->id, 'resident_id' => $member->id],
                        [
                            'first_name' => $member->first_name,
                            'last_name' => $member->last_name,
                            'email' => $residentEmails[$residentIndex],
                            'birth_date' => $member->birth_date,
                            'relationship' => $relationship,
                        ]
                    );
                }

                if (count($householdInfo['members']) === 3 && $householdInfo['household_code'] === 'HH-2026-0002') {
                    HouseholdMember::updateOrCreate(
                        ['household_id' => $household->id, 'resident_id' => null, 'email' => 'lito.child@barangay.test'],
                        [
                            'first_name' => 'Lito',
                            'last_name' => 'Santos',
                            'email' => 'lito.child@barangay.test',
                            'relationship' => 'Child',
                            'birth_date' => now()->subYears(10)->format('Y-m-d'),
                        ]
                    );
                }

                $this->logAudit($staffUser, 'households', 'created', $household->id, $household->toArray());
            }

            $petData = [];
            for ($i = 0; $i < 25; $i++) {
                $petData[] = [
                    'resident' => $i % 40,
                    'nickname' => ['Milo', 'Luna', 'Sparky', 'Chico', 'Bella', 'Toby', 'Coco', 'Max', 'Mimi', 'Rocky'][$i % 10] . ' ' . ($i >= 10 ? ($i + 1) : ''),
                    'species' => ['Dog', 'Cat', 'Bird', 'Rabbit'][$i % 4],
                    'breed' => ['Shih Tzu', 'Japanese Bobtail', 'Beagle', 'Pug', 'Persian', 'Bulldog', 'Labrador', 'Mixed'][$i % 8],
                    'sex' => ($i % 2 === 0) ? 'male' : 'female',
                    'color' => ['Brown', 'White', 'Black', 'Gray', 'Calico', 'Tri-color', 'Fawn'][$i % 7],
                    'birth_date' => now()->subYears(rand(1, 5))->format('Y-m-d'),
                ];
            }

            foreach ($petData as $petInfo) {
                $pet = Pet::updateOrCreate(
                    ['resident_id' => $residents[$petInfo['resident']]->id, 'nickname' => $petInfo['nickname']],
                    [
                        'species' => $petInfo['species'],
                        'breed' => $petInfo['breed'],
                        'birth_date' => $petInfo['birth_date'],
                        'sex' => $petInfo['sex'],
                        'color' => $petInfo['color'],
                        'vaccination_status' => 'up-to-date',
                    ]
                );
                $this->logAudit($staffUser, 'pets', 'created', $pet->id, $pet->toArray());
            }

            $term = BarangayTerm::updateOrCreate(
                ['title' => 'Barangay Term 2023-2026'],
                [
                    'term_start' => 2023,
                    'term_end' => 2026,
                    'is_active' => true,
                    'is_archived' => false,
                    'notes' => 'Current barangay term.',
                ]
            );

            $this->logAudit($staffUser, 'barangay_terms', 'created', $term->id, $term->toArray());

            $officialsData = [
                [
                    'name' => 'Sherlyn Quider',
                    'position' => 'Barangay Captain',
                    'committee' => 'Leadership',
                    'contact_no' => '091235467890',
                    'email' => 'captain@barangay.test',
                ],
                [
                    'name' => 'Rolando Cruz',
                    'position' => 'Barangay Secretary',
                    'committee' => 'Administration',
                    'contact_no' => '091234567890',
                    'email' => 'secretary@barangay.test',
                ],
                [
                    'name' => 'Arnel Santos',
                    'position' => 'Barangay Treasurer',
                    'committee' => 'Finance',
                    'contact_no' => '091234567891',
                    'email' => 'treasurer@barangay.test',
                ],
                [
                    'name' => 'Liza Reyes',
                    'position' => 'Kagawad - Peace & Order',
                    'committee' => 'Peace & Order',
                    'contact_no' => '091234567892',
                    'email' => 'peace@barangay.test',
                ],
                [
                    'name' => 'Manny Dela Cruz',
                    'position' => 'Kagawad - Health',
                    'committee' => 'Health & Sanitation',
                    'contact_no' => '091234567893',
                    'email' => 'health@barangay.test',
                ],
                [
                    'name' => 'Rhea Mendoza',
                    'position' => 'Kagawad - Infrastructure',
                    'committee' => 'Infrastructure',
                    'contact_no' => '091234567894',
                    'email' => 'infra@barangay.test',
                ],
                [
                    'name' => 'Jun Leyson',
                    'position' => 'Kagawad - Youth & Sports',
                    'committee' => 'Youth',
                    'contact_no' => '091234567895',
                    'email' => 'youth@barangay.test',
                ],
                [
                    'name' => 'Cecilia Ramos',
                    'position' => 'Sangguniang Kabataan Chairperson',
                    'committee' => 'Youth & Education',
                    'contact_no' => '091234567896',
                    'email' => 'skchair@barangay.test',
                ],
            ];

            foreach ($officialsData as $officialData) {
                $official = BarangayOfficial::updateOrCreate(
                    ['email' => $officialData['email']],
                    array_merge($officialData, [
                        'photo_path' => null,
                        'barangay_term_id' => $term->id,
                        'notes' => 'Seeded barangay official record.',
                    ])
                );
                $this->logAudit($staffUser, 'barangay_officials', 'created', $official->id, $official->toArray());
            }

            $eventsData = [];
            $eventTypes = ['program', 'health', 'campaign', 'sports', 'celebration', 'training', 'meeting'];
            $locations = ['Barangay Hall', 'Purok 1 Chapel', 'Purok 2 Basketball Court', 'Purok 3 Multi-Purpose Court', 'Purok 4 Community Center', 'Barangay Covered Court', 'Barangay Plaza'];
            for ($i = 0; $i < 25; $i++) {
                $eventsData[] = [
                    'title' => 'Event ' . ($i + 1) . ' - ' . ucfirst($eventTypes[$i % 7]),
                    'description' => 'Description for event ' . ($i + 1) . ' in the barangay.',
                    'start_datetime' => now()->addDays(7 + $i * 3)->startOfDay()->addHours(8 + ($i % 8)),
                    'end_datetime' => now()->addDays(7 + $i * 3)->startOfDay()->addHours(12 + ($i % 4)),
                    'location' => $locations[$i % 7],
                    'type' => $eventTypes[$i % 7],
                    'is_all_day' => false,
                    'reminder' => '1day',
                ];
            }

            $events = [];
            foreach ($eventsData as $eventData) {
                $events[] = Event::updateOrCreate(
                    ['title' => $eventData['title']],
                    array_merge($eventData, [
                        'is_published' => true,
                        'created_by' => $staffUser->id,
                    ])
                );
                $this->logAudit($staffUser, 'events', 'created', end($events)->id, end($events)->toArray());
            }

            $announcementsData = [];
            $announcementTypes = ['event', 'campaign', 'training', 'program', 'health', 'meeting', 'general'];
            for ($i = 0; $i < 25; $i++) {
                $announcementsData[] = [
                    'title' => 'Announcement ' . ($i + 1) . ' - ' . ucfirst($announcementTypes[$i % 7]),
                    'content' => 'Content for announcement ' . ($i + 1) . '. This is an important update for the barangay residents.',
                    'type' => $announcementTypes[$i % 7],
                    'event_id' => $events[$i]->id ?? null,
                ];
            }

            foreach ($announcementsData as $announcementData) {
                $announcement = Announcement::updateOrCreate(
                    ['title' => $announcementData['title']],
                    array_merge($announcementData, [
                        'publish_date' => now()->format('Y-m-d'),
                        'expire_date' => now()->addDays(30)->format('Y-m-d'),
                        'is_published' => true,
                        'created_by' => $staffUser->id,
                    ])
                );
                $this->logAudit($staffUser, 'announcements', 'created', $announcement->id, $announcement->toArray());
            }

            $blotterTypes = ['Noise Disturbance', 'Theft', 'Vandalism', 'Barangay Brawl', 'Traffic Violation',
                'Domestic Dispute', 'Illegal Dumping', 'Unauthorized Gathering', 'Animal Complaint', 'Property Damage',
                'Harassment', 'Trespassing', 'Public Intoxication', 'Disorderly Conduct', 'Pet Complaint'];
            $locations2 = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'City Road', 'Barangay Hall', 'Poblacion Area'];
            $blotterIncidents = [];
            for ($i = 0; $i < 25; $i++) {
                $blotterIncidents[] = [
                    $blotterTypes[$i % 15],
                    $locations2[$i % 7],
                    $i % 40,
                    ($i + 5) % 40,
                ];
            }

            foreach ($blotterIncidents as $index => $incidentInfo) {
                $blotterNumber = sprintf('BLT-2026-%04d', $index + 1);
                $complainant = $residents[$incidentInfo[2]];
                $respondent = $residents[$incidentInfo[3]];
                $blotter = Blotter::updateOrCreate(
                    ['blotter_no' => $blotterNumber],
                    [
                        'incident_date' => now()->subDays(10 - ($index % 10)),
                        'incident_type' => $incidentInfo[0],
                        'incident_location' => $incidentInfo[1],
                        'narrative' => "Reported incident of {$incidentInfo[0]} at {$incidentInfo[1]}",
                        'status' => 'filed',
                        'remarks' => 'Under investigation by barangay staff.',
                        'complainant_resident_id' => $complainant->id,
                        'respondent_resident_id' => $respondent->id,
                        'complainant_name' => $complainant->first_name . ' ' . $complainant->last_name,
                        'respondent_name' => $respondent->first_name . ' ' . $respondent->last_name,
                        'complainant_contact' => $complainant->contact_no,
                        'respondent_contact' => $respondent->contact_no,
                        'recorded_by' => $staffUser->id,
                    ]
                );
                $this->logAudit($staffUser, 'blotters', 'created', $blotter->id, $blotter->toArray());

                $caseNumber = sprintf('CASE-2026-%04d', $index + 1);
                $case = CaseFile::updateOrCreate(
                    ['case_no' => $caseNumber],
                    [
                        'blotter_id' => $blotter->id,
                        'status' => 'scheduled',
                        'opened_at' => now()->subDays(9 - ($index % 10)),
                        'closed_at' => null,
                        'resolution_summary' => null,
                        'handled_by' => $staffUser->id,
                    ]
                );
                $this->logAudit($staffUser, 'cases', 'created', $case->id, $case->toArray());

                $hearingDates = [
                    now()->addDays(2 + $index),
                    now()->addDays(9 + $index),
                ];
                if ($index % 2 === 0) {
                    $hearingDates[] = now()->addDays(16 + $index);
                }

                foreach ($hearingDates as $hearingIndex => $scheduledAt) {
                    $hearing = CaseHearing::updateOrCreate(
                        ['case_id' => $case->id, 'scheduled_at' => $scheduledAt],
                        [
                            'location' => 'Barangay Hall',
                            'status' => 'scheduled',
                            'notes' => 'Hearing #' . ($hearingIndex + 1),
                            'result' => null,
                        ]
                    );
                    $this->logAudit($staffUser, 'case_schedules', 'created', $hearing->id, $hearing->toArray());
                }
            }
        });
    }

    private function logAudit(User $actor, string $module, string $action, int $recordId, ?array $newData = null, ?array $oldData = null): void
    {
        AuditLog::create([
            'user_id' => $actor->id,
            'module' => $module,
            'action' => $action,
            'record_id' => $recordId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => '127.0.0.1',
        ]);
    }
}
