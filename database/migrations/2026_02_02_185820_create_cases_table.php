<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();

            $table->string('case_no')->unique();

            $table->foreignId('blotter_id')
                ->constrained('blotters')
                ->cascadeOnDelete()
                ->unique(); // ✅ 1 blotter -> max 1 case

            $table->string('status')->default('open')->index();
            // open | scheduled | settled | referred | dismissed | archived

            $table->dateTime('opened_at')->useCurrent();
            $table->dateTime('closed_at')->nullable();

            $table->text('resolution_summary')->nullable();

            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
