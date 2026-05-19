<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('residents')
            ->whereNotNull('photo_path')
            ->whereNull('id_image_path')
            ->update(['id_image_path' => DB::raw('photo_path')]);
    }

    public function down(): void
    {
        // No down needed - data transfer only
    }
};
