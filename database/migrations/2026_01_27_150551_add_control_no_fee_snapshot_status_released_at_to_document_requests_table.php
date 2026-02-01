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
        Schema::table('document_requests', function (Blueprint $table) {

            if (!Schema::hasColumn('document_requests', 'control_no')) {
                $table->string('control_no')->unique()->after('id');
            }

            if (!Schema::hasColumn('document_requests', 'fee_snapshot')) {
                $table->decimal('fee_snapshot', 10, 2)->default(0)->after('purpose');
            }

            if (!Schema::hasColumn('document_requests', 'status')) {
                $table->enum('status', ['pending','released','cancelled'])->default('pending')->after('fee_snapshot');
            }

            if (!Schema::hasColumn('document_requests', 'released_at')) {
                $table->timestamp('released_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            if (Schema::hasColumn('document_requests', 'released_at')) $table->dropColumn('released_at');
            if (Schema::hasColumn('document_requests', 'status')) $table->dropColumn('status');
            if (Schema::hasColumn('document_requests', 'fee_snapshot')) $table->dropColumn('fee_snapshot');
            if (Schema::hasColumn('document_requests', 'control_no')) $table->dropUnique(['control_no']);
            if (Schema::hasColumn('document_requests', 'control_no')) $table->dropColumn('control_no');
        });
    }
};
