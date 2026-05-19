<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('case_hearings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')->constrained('cases')->cascadeOnDelete();

            $table->dateTime('scheduled_at')->index();
            $table->string('location')->default('Barangay Hall');

            $table->string('status')->default('scheduled')->index();
            // scheduled | done | cancelled | no_show

            $table->longText('notes')->nullable();
            $table->longText('result')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_hearings');
    }
};
