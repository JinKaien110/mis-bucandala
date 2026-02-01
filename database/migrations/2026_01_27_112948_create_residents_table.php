<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();

            // Basic Identity
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');

            $table->enum('sex', ['male','female'])->nullable();
            $table->date('birth_date')->nullable();

            // Address (PH-friendly)
            $table->string('address_line'); // block/lot/street/purok/phase
            $table->string('barangay')->default('Bucandala 1');
            $table->string('city')->default('Imus');
            $table->string('province')->default('Cavite');

            // Contact + other details (optional for now)
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->string('civil_status')->nullable(); // single/married/etc (keep flexible)
            $table->string('occupation')->nullable();

            // Verification (future-ready, not enforced)
            $table->string('verification_id')->nullable();
            $table->enum('verification_type', [
                'barangay_id',
                'national_id',
                'passport',
                'drivers_license',
            ])->nullable();

            $table->enum('verification_status', [
                'unverified',
                'pending',
                'verified',
                'rejected',
            ])->default('unverified');

            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            // Record status
            $table->enum('status', ['active','inactive'])->default('active');

            $table->timestamps();

            // Helpful index for search
            $table->index(['last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
