<?php
// database/migrations/2026_01_28_000001_create_blotters_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blotters', function (Blueprint $table) {
            $table->id();

            $table->string('blotter_no')->unique();

            $table->dateTime('incident_date');
            $table->string('incident_type');
            $table->string('incident_location');
            $table->longText('narrative');

            $table->string('status')->default('filed');
            $table->longText('remarks')->nullable();

            // resident link (optional)
            $table->foreignId('complainant_resident_id')->nullable()->constrained('residents')->nullOnDelete();
            $table->foreignId('respondent_resident_id')->nullable()->constrained('residents')->nullOnDelete();

            // fallback names (for non-residents / walk-ins)
            $table->string('complainant_name')->nullable();
            $table->string('respondent_name')->nullable();
            $table->string('complainant_contact')->nullable();
            $table->string('respondent_contact')->nullable();

            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            $table->index(['status', 'incident_date']);
            $table->index(['incident_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blotters');
    }
};
