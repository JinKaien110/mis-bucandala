<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            if (Schema::hasColumn('households', 'employment_status')) {
                $table->dropColumn('employment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            // Restore column (approximate previous type)
            if (!Schema::hasColumn('households', 'employment_status')) {
                $table->string('employment_status', 50)->nullable()->after('monthly_income_range');
            }
        });
    }
};

