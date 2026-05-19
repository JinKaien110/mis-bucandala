<?php
// database/migrations/2026_02_02_000011_add_public_verification_fields_to_residents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('residents', function (Blueprint $table) {
      if (!Schema::hasColumn('residents', 'household_id')) {
        $table->foreignId('household_id')->nullable()->after('id')->constrained('households')->nullOnDelete();
      }

      if (!Schema::hasColumn('residents', 'verification_status')) {
        $table->string('verification_status')->default('auto_verified'); // auto_verified | flagged
      }

      if (!Schema::hasColumn('residents', 'registered_via')) {
        $table->string('registered_via')->default('admin'); // admin | public_form
      }

      if (!Schema::hasColumn('residents', 'id_image_path')) {
        $table->string('id_image_path')->nullable();
      }

      if (!Schema::hasColumn('residents', 'selfie_image_path')) {
        $table->string('selfie_image_path')->nullable();
      }

      if (!Schema::hasColumn('residents', 'otp_email')) {
        $table->string('otp_email')->nullable();
      }

      if (!Schema::hasColumn('residents', 'otp_verified_at')) {
        $table->timestamp('otp_verified_at')->nullable();
      }
    });
  }

  public function down(): void {
    Schema::table('residents', function (Blueprint $table) {
      // keep safe: only drop if you want to rollback fully
      // $table->dropConstrainedForeignId('household_id');
      // $table->dropColumn(['verification_status','registered_via','id_image_path','selfie_image_path','otp_email','otp_verified_at']);
    });
  }
};
