<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('announcements', 'show_on_calendar')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->boolean('show_on_calendar')->default(false)->after('is_published');
            });
        }
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('show_on_calendar');
        });
    }
};