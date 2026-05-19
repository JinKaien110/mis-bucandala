<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Duplicate migration - archived_at already added in 2026_03_02_000001_add_archived_at_to_all_tables
        return;
        Schema::table('residents', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('status');
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropIndex(['archived_at']);
            $table->dropColumn('archived_at');
        });
    }
};
