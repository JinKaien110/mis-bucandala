<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            // Ensure column becomes VARCHAR so it can store string/range keys.
            // Using string(50) as a safe default.
            if (Schema::hasColumn('residents', 'monthly_income')) {
                $table->string('monthly_income', 50)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            if (Schema::hasColumn('residents', 'monthly_income')) {
                // Best-effort revert to a numeric-friendly type.
                // If your original type was different, adjust accordingly.
                $table->integer('monthly_income')->nullable()->change();
            }
        });
    }
};

