<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // link created user/admin account to the barangay_official record
            $table->unsignedBigInteger('barangay_official_id')->nullable()->after('user_id');

            $table->foreign('barangay_official_id')
                ->references('id')
                ->on('barangay_officials')
                ->nullOnDelete();

            $table->index('barangay_official_id');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // drop FK first
            $table->dropForeign(['barangay_official_id']);
            $table->dropIndex(['barangay_official_id']);
            $table->dropColumn('barangay_official_id');
        });
    }
};

