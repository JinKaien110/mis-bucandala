<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            // Change from ENUM to VARCHAR so it can store exact values from the form
            if (Schema::hasColumn('households', 'monthly_income_range')) {
                $table->string('monthly_income_range', 50)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // Revert back to a compatible ENUM set (as it existed before)
        // NOTE: If you previously used different enum values, adjust this list.
        Schema::table('households', function (Blueprint $table) {
            if (Schema::hasColumn('households', 'monthly_income_range')) {
                $table->enum('monthly_income_range', [
                    'No Income',
                    'Below 5,000',
                    '5,001 - 10,000',
                    '10,001 - 20,000',
                    '20,001 - 30,000',
                    '30,001 - 50,000',
                    '50,001 - 100,000',
                    'Above 100,000',
                ])->nullable()->change();
            }
        });
    }
};


