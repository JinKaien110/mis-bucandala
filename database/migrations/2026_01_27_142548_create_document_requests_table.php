<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('document_requests', function (Blueprint $table) {
        $table->id();

        $table->string('control_no')->unique();

        $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
        $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();

        $table->string('purpose')->nullable();
        $table->text('remarks')->nullable();

        $table->decimal('fee_amount', 10, 2)->default(0);

        $table->enum('status', ['pending','released','cancelled'])->default('pending');

        $table->foreignId('requested_by')->nullable()->constrained('users');
        $table->string('requested_via')->default('staff');

        $table->foreignId('released_by')->nullable()->constrained('users');
        $table->timestamp('released_at')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
