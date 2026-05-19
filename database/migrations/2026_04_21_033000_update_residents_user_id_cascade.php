<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing foreign key if exists
        try {
            DB::statement('ALTER TABLE residents DROP FOREIGN KEY IF EXISTS residents_user_id_foreign');
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::statement('ALTER TABLE residents ADD CONSTRAINT residents_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // May already exist
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE residents DROP FOREIGN KEY IF EXISTS residents_user_id_foreign');
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::statement('ALTER TABLE residents ADD CONSTRAINT residents_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Ignore
        }
    }
};
