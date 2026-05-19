<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('household_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('resident_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->text('message')->nullable();
            $table->foreignId('responded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            // Unique constraint: one resident can only have one pending request per household
            $table->unique(['household_id', 'resident_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('household_join_requests');
    }
};
