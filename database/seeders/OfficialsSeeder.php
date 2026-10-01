<?php

namespace Database\Seeders;

use App\Models\BarangayOfficial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OfficialsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $officialsData = [
                [
                    'first_name' => 'Ferdinand A.',
                    'last_name' => 'Santiaguel',
                    'position' => 'Punong Barangay',
                    'committee' => 'Punong Barangay',
                    'contact_no' => '09000000001',
                    'email' => 'ferdinand.santiaguel@barangay.test',
                    'photo_path' => 'officials/ferdinand-a-santiaguel.jpg',
                    'barangay_term_id' => 1
                ],
                [
                    'first_name' => 'Excel',
                    'last_name' => 'Lapidario',
                    'position' => 'Barangay Councilor',
                    'committee' => 'Human Rights, Public Order and Good Governance',
                    'contact_no' => '09000000002',
                    'email' => 'excel.lapidario@barangay.test',
                    'photo_path' => 'officials/excel-lapidario.jpg',
                    'barangay_term_id' => 1
                ],
                [
                    'first_name' => 'Eduardo',
                    'last_name' => 'Naval',
                    'position' => 'Barangay Councilor',
                    'committee' => 'Livelihood, Employment and Cooperative Development',
                    'contact_no' => '09000000003',
                    'email' => 'eduardo.naval@barangay.test',
                    'photo_path' => 'officials/eduardo-naval.jpg',
                    'barangay_term_id' => 1
                ],
                [
                    'first_name' => 'Jeffrey',
                    'last_name' => 'Cadaba',
                    'position' => 'Barangay Councilor',
                    'committee' => 'Appropriation',
                    'contact_no' => '09000000004',
                    'email' => 'jeffrey.cadaba@barangay.test',
                    'photo_path' => 'officials/jeffrey-cadaba.jpg',
                    'barangay_term_id' => 1
                ],
                [
                    'first_name' => 'Charles Limuel M.',
                    'last_name' => 'Santiaguel',
                    'position' => 'Barangay Councilor',
                    'committee' => 'Public Works and Infrastructure',
                    'contact_no' => '09000000005',
                    'email' => 'charles.santiaguel@barangay.test',
                    'photo_path' => 'officials/charles-limuel-m-santiaguel.jpg',
                    'barangay_term_id' => 1
                ],
                [
                    'first_name' => 'Fernando',
                    'last_name' => 'Lasquete',
                    'position' => 'Barangay Councilor',
                    'committee' => 'Environmental Protection',
                    'contact_no' => '09000000006',
                    'email' => 'fernando.lasquete@barangay.test',
                    'photo_path' => 'officials/fernando-lasquete.jpg',
                    'barangay_term_id' => 1
                ],
                [
                    'first_name' => 'Jobert',
                    'last_name' => 'Telic',
                    'position' => 'Barangay Councilor',
                    'committee' => 'Education and Health Sanitation',
                    'contact_no' => '09000000007',
                    'email' => 'jobert.telic@barangay.test',
                    'photo_path' => 'officials/jobert-telic.jpg',
                    'barangay_term_id' => 1
                ],
                [
                    'first_name' => 'Gina',
                    'last_name' => 'Jimenez',
                    'position' => 'Barangay Councilor',
                    'committee' => 'Women and Family Welfare; Violence Against Women and Children',
                    'contact_no' => '09000000008',
                    'email' => 'gina.jimenez@barangay.test',
                    'photo_path' => 'officials/gina-jimenez.jpg',
                    'barangay_term_id' => 1
                ],
                [
                    'first_name' => 'Victoria',
                    'last_name' => 'Aranzasu',
                    'position' => 'Barangay Secretary',
                    'committee' => 'Barangay Secretary',
                    'contact_no' => '09000000009',
                    'email' => 'victoria.aranzasu@barangay.test',
                    'photo_path' => 'officials/victoria-aranzasu.jpg',
                    'barangay_term_id' => 1
                ],
            ];

            foreach ($officialsData as $official) {
                BarangayOfficial::updateOrCreate(
                    ['email' => $official['email']],
                    $official
                );
            }
        });
    }
}