<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename officials to barangay_officials
        Schema::rename('officials', 'barangay_officials');
        
        // Rename official_terms to barangay_terms
        Schema::rename('official_terms', 'barangay_terms');
        
        // Update foreign key references in barangay_officials
        Schema::table('barangay_officials', function (Blueprint $table) {
            $table->renameColumn('term_id', 'barangay_term_id');
            $table->foreign('barangay_term_id')
                ->references('id')
                ->on('barangay_terms')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Revert foreign key changes
        Schema::table('barangay_officials', function (Blueprint $table) {
            $table->dropForeign(['barangay_term_id']);
            $table->renameColumn('barangay_term_id', 'term_id');
        });
        
        // Rename tables back
        Schema::rename('barangay_terms', 'official_terms');
        Schema::rename('barangay_officials', 'officials');
    }
};
