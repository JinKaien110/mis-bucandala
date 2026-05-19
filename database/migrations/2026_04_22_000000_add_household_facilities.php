<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Duplicate migration - facilities already added in 2026_04_21_130000
        // This migration was accidentally duplicated, skipping to avoid errors
    }

    public function down(): void
    {
        // No-op - facilities were not added by this migration
    }
};
