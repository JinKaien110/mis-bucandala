<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            // Address fields
            $table->string('street')->nullable()->after('address_line');
            $table->string('phase')->nullable()->after('street');

            // 1. Basic Household Information
            $table->enum('household_type', ['Family', 'Extended Family', 'Boarding / Rental'])->nullable()->after('phase');
            $table->enum('homeownership_type', ['Owned', 'Rented', 'Informal Settler'])->nullable()->after('household_type');

            // 2. Household Members Summary
            $table->unsignedTinyInteger('total_members')->nullable()->default(0)->after('homeownership_type');
            $table->unsignedTinyInteger('total_adults')->nullable()->default(0)->after('total_members');
            $table->unsignedTinyInteger('total_minors')->nullable()->default(0)->after('total_adults');
            $table->unsignedTinyInteger('total_senior_citizens')->nullable()->default(0)->after('total_minors');
            $table->unsignedTinyInteger('total_pwd')->nullable()->default(0)->after('total_senior_citizens');

            // 3. Household Contact
            $table->string('contact_no', 20)->nullable()->after('total_pwd');

            // 4. Socio-Economic Information
            $table->enum('monthly_income_range', [
                'No Income',
                'Below 5,000',
                '5,001 - 10,000',
                '10,001 - 20,000',
                '20,001 - 30,000',
                '30,001 - 50,000',
                '50,001 - 100,000',
                'Above 100,000',
            ])->nullable()->after('contact_no');
            $table->enum('employment_status', ['Employed', 'Unemployed', 'Self-employed'])->nullable()->after('monthly_income_range');
            $table->string('primary_income_source', 150)->nullable()->after('employment_status');
            $table->boolean('is_4ps_beneficiary')->nullable()->default(false)->after('primary_income_source');
            $table->boolean('is_indigent')->nullable()->default(false)->after('is_4ps_beneficiary');

            // 5. Health & Community Indicators
            $table->boolean('has_pregnant_member')->nullable()->default(false)->after('is_indigent');
            $table->boolean('has_senior_citizen')->nullable()->default(false)->after('has_pregnant_member');
            $table->boolean('has_pwd')->nullable()->default(false)->after('has_senior_citizen');
            $table->boolean('has_chronic_illness')->nullable()->default(false)->after('has_pwd');

            // 6. Housing & Utilities
            $table->enum('house_type', ['Concrete', 'Semi-concrete', 'Light materials'])->nullable()->after('has_chronic_illness');
            $table->boolean('has_electricity')->nullable()->default(true)->after('house_type');
            $table->boolean('has_toilet')->nullable()->default(false)->after('has_electricity');
            $table->boolean('has_bathroom')->nullable()->default(false)->after('has_toilet');
            $table->boolean('has_kitchen')->nullable()->default(false)->after('has_bathroom');
            $table->boolean('has_garage')->nullable()->default(false)->after('has_kitchen');

            // 7. Community-Related Data
            $table->unsignedInteger('registered_pets_count')->nullable()->default(0)->after('has_garage');
            $table->text('barangay_program_participation')->nullable()->after('registered_pets_count');
            $table->string('disaster_risk_level', 50)->nullable()->after('barangay_program_participation');
        });
    }

    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropColumn([
                'household_type',
                'homeownership_type',
                'total_members',
                'total_adults',
                'total_minors',
                'total_senior_citizens',
                'total_pwd',
                'contact_no',
                'monthly_income_range',
                'street',
                'phase',
                'employment_status',
                'primary_income_source',
                'is_4ps_beneficiary',
                'is_indigent',
                'has_pregnant_member',
                'has_senior_citizen',
                'has_pwd',
                'has_chronic_illness',
                'house_type',
                'has_electricity',
                'has_toilet',
                'has_bathroom',
                'has_kitchen',
                'has_garage',
                'registered_pets_count',
                'barangay_program_participation',
                'disaster_risk_level',
            ]);
        });
    }
};
