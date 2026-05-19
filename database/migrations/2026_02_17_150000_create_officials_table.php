<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position'); // Captain, Councilor, SK Chair, Secretary, Treasurer, etc.
            $table->string('committee')->nullable(); // Committee assignment
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->string('photo_path')->nullable();
            $table->date('term_start');
            $table->date('term_end')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'is_archived']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officials');
    }
};
