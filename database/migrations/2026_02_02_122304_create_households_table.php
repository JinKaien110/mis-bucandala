<?php
// database/migrations/2026_02_02_000010_create_households_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('households', function (Blueprint $table) {
      $table->id();

      $table->string('household_code')->unique(); // HH-2026-000001
      $table->string('address_line')->nullable(); // block/lot/street etc.
      $table->string('purok')->nullable();

      $table->string('head_name')->nullable(); // optional
      $table->unsignedBigInteger('head_resident_id')->nullable(); // optional link later

      $table->timestamps();

      $table->index(['purok']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('households');
  }
};
