<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('household_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('resident_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('relationship');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('household_members');
    }
};