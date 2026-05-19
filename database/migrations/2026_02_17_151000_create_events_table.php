<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->default('general'); // general, meeting, program, reminder
            $table->boolean('is_all_day')->default(false);
            $table->boolean('is_published')->default(true);
            $table->string('reminder')->nullable(); // 15min, 30min, 1hour, 1day
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index('start_datetime');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
