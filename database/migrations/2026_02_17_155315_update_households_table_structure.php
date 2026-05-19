<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            // Drop old columns
            if (Schema::hasColumn('households', 'head_name')) {
                $table->dropColumn('head_name');
            }

            if (Schema::hasColumn('households', 'head_resident_id')) {
                $table->dropColumn('head_resident_id');
            }

            // Set default purok
            $table->string('purok')->default('Bucandala 1')->change();
        });
    }

    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->string('head_name')->nullable();
            $table->unsignedBigInteger('head_resident_id')->nullable();
            $table->string('purok')->nullable()->change();
        });
    }
};
