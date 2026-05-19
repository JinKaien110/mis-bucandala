<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('official_terms', function (Blueprint $table) {
            $table->id();
            $table->year('term_start');
            $table->year('term_end')->nullable();
            $table->string('title')->nullable(); // e.g., "Term 2023-2026"
            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'is_archived']);
        });

        // Add foreign key to officials table
        Schema::table('officials', function (Blueprint $table) {
            $table->foreignId('term_id')
                ->nullable()
                ->constrained('official_terms')
                ->onDelete('set null');
            
            // Remove old columns that will now be in term
            $table->dropColumn(['term_start', 'term_end', 'is_active', 'is_archived']);
        });
    }

    public function down(): void
    {
        Schema::table('officials', function (Blueprint $table) {
            $table->date('term_start')->nullable();
            $table->date('term_end')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->dropForeign(['term_id']);
            $table->dropColumn('term_id');
        });

        Schema::dropIfExists('official_terms');
    }
};
