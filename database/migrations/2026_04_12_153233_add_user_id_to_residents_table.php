<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // Drop existing constraint if any
            DB::statement('ALTER TABLE residents DROP FOREIGN KEY IF EXISTS residents_user_id_foreign');
            // Add new constraint with CASCADE delete
            DB::statement('ALTER TABLE residents ADD CONSTRAINT residents_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Foreign key may already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE residents DROP FOREIGN KEY IF EXISTS residents_user_id_foreign');
        } catch (\Exception $e) {
            // Ignore
        }
    }
};
