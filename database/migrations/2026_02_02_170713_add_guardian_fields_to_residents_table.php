<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('residents', function (Blueprint $table) {
      $table->boolean('is_minor')->default(false)->after('birth_date');

      $table->string('guardian_full_name', 150)->nullable()->after('email');
      $table->string('guardian_email', 255)->nullable()->after('guardian_full_name');
      $table->string('guardian_contact_no', 30)->nullable()->after('guardian_email');
      $table->string('guardian_relationship', 50)->nullable()->after('guardian_contact_no');

      $table->string('child_doc_path')->nullable()->after('selfie_image_path');
    });
  }

  public function down(): void
  {
    Schema::table('residents', function (Blueprint $table) {
      $table->dropColumn([
        'is_minor',
        'guardian_full_name',
        'guardian_email',
        'guardian_contact_no',
        'guardian_relationship',
        'child_doc_path',
      ]);
    });
  }
};
