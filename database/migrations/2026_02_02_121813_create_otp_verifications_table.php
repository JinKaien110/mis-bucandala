<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('otp_verifications', function (Blueprint $table) {
      $table->id();
      $table->string('purpose'); // resident_registration, pet_registration
      $table->string('email')->nullable();
      $table->string('phone')->nullable();
      $table->string('otp_hash');
      $table->timestamp('expires_at');
      $table->timestamp('verified_at')->nullable();
      $table->unsignedSmallInteger('attempts')->default(0);
      $table->timestamp('last_sent_at')->nullable();
      $table->string('request_ip')->nullable();
      $table->timestamps();

      $table->index(['purpose', 'email']);
      $table->index(['purpose', 'phone']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('otp_verifications');
  }
};
