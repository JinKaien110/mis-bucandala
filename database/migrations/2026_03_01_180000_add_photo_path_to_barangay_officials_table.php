<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangay_officials', function (Blueprint $table) {
            if (!Schema::hasColumn('barangay_officials', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('barangay_officials', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
