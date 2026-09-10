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
        Schema::table('residents', function (Blueprint $table) {
            $table->string('proof_of_billing_path')->nullable()->after('selfie_image_path');
            $table->string('employment_status')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->string('educational_attainment')->nullable();
            $table->boolean('solo_parent')->default(false);
            $table->boolean('pwd')->default(false);
            $table->boolean('indigent')->default(false);
            $table->boolean('four_ps_beneficiary')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn('proof_of_billing_path');
            $table->dropColumn('employment_status');
            $table->dropColumn('monthly_income');
            $table->dropColumn('educational_attainment');
            $table->dropColumn('solo_parent');
            $table->dropColumn('pwd');
            $table->dropColumn('indigent');
             $table->dropColumn('four_ps_beneficiary');
        });
    }
};
