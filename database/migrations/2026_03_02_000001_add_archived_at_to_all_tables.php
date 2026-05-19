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
        $tables = [
            'residents',
            'announcements',
            'officials',
            'barangay_officials',
            'events',
            'blotters',
            'cases',
            'document_types',
            'document_requests',
            'households',
            'pets',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'archived_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->timestamp('archived_at')->nullable()->after('updated_at');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'residents',
            'announcements',
            'officials',
            'barangay_officials',
            'events',
            'blotters',
            'cases',
            'document_types',
            'document_requests',
            'households',
            'pets',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'archived_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('archived_at');
                });
            }
        }
    }
};
