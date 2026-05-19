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
        Schema::table('document_types', function (Blueprint $table) {
            if (!Schema::hasColumn('document_types', 'fee')) {
                $table->decimal('fee', 10, 2)->default(0)->after('name');
            }

            if (!Schema::hasColumn('document_types', 'status')) {
                $table->enum('status', ['active','inactive'])->default('active')->after('fee');
            }

            if (!Schema::hasColumn('document_types', 'template_path')) {
                $table->string('template_path')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            if (Schema::hasColumn('document_types', 'template_path')) $table->dropColumn('template_path');
            if (Schema::hasColumn('document_types', 'status')) $table->dropColumn('status');
            if (Schema::hasColumn('document_types', 'fee')) $table->dropColumn('fee');
        });
    }
};
