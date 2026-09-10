<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->string('monthly_income')->nullable();
            $table->string('employment_status')->nullable();
            $table->boolean('pwd_status')->default(false);
            $table->boolean('senior_citizen_status')->default(false);
            $table->boolean('solo_parent')->default(false);
            $table->boolean('four_ps_beneficiary')->default(false);
            $table->boolean('indigent_status')->default(false);
            $table->string('educational_attainment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn([
                'monthly_income',
                'employment_status',
                'pwd_status',
                'senior_citizen_status',
                'solo_parent',
                'four_ps_beneficiary',
                'indigent_status',
                'educational_attainment',
            ]);
        });
    }
};
