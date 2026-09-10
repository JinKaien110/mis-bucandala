<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('household_members', function (Blueprint $table) {
            $table->boolean('is_4ps_beneficiary')->nullable()->default(false)->after('is_pwd');
            $table->boolean('is_indigent')->nullable()->default(false)->after('is_4ps_beneficiary');
            $table->boolean('has_pregnant_member')->nullable()->default(false)->after('is_indigent');
            $table->boolean('has_senior_citizen')->nullable()->default(false)->after('has_pregnant_member');
            $table->boolean('has_chronic_illness')->nullable()->default(false)->after('has_senior_citizen');
        });
    }

    public function down(): void
    {
        Schema::table('household_members', function (Blueprint $table) {
            $table->dropColumn([
                'is_4ps_beneficiary',
                'is_indigent',
                'has_pregnant_member',
                'has_senior_citizen',
                'has_chronic_illness',
            ]);
        });
    }
};
