<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('blotters', function (Blueprint $table) {
            $table->string('complainant_email', 255)->nullable()->after('complainant_contact');
            $table->string('respondent_email', 255)->nullable()->after('respondent_contact');
        });
    }

    public function down(): void
    {
        Schema::table('blotters', function (Blueprint $table) {
            $table->dropColumn(['complainant_email', 'respondent_email']);
        });
    }
};

