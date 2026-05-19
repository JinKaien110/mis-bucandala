<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->string('nickname');
            $table->string('species'); // dog, cat, bird, etc.
            $table->string('breed')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('sex', ['male', 'female']);
            $table->string('color')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('vaccination_status')->nullable(); // up-to-date, overdue, not vaccinated
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('resident_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
