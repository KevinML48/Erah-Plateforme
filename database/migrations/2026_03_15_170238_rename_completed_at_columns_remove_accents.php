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
        // Drop and recreate indexes, then rename columns
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, use unquoted names or backticks
            DB::statement("ALTER TABLE user_missions RENAME COLUMN complèted_at TO completed_at");
            DB::statement("ALTER TABLE user_guided_tours RENAME COLUMN complèted_at TO completed_at");
            DB::statement("ALTER TABLE mission_completions RENAME COLUMN complèted_at TO completed_at");
        } else {
            // For MySQL/PostgreSQL use schema builder
            DB::statement('ALTER TABLE user_missions CHANGE COLUMN `complèted_at` `completed_at` TIMESTAMP NULL');
            DB::statement('ALTER TABLE user_guided_tours CHANGE COLUMN `complèted_at` `completed_at` TIMESTAMP NULL');
            DB::statement('ALTER TABLE mission_completions CHANGE COLUMN `complèted_at` `completed_at` TIMESTAMP NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("ALTER TABLE user_missions RENAME COLUMN completed_at TO complèted_at");
            DB::statement("ALTER TABLE user_guided_tours RENAME COLUMN completed_at TO complèted_at");
            DB::statement("ALTER TABLE mission_completions RENAME COLUMN completed_at TO complèted_at");
        } else {
            DB::statement('ALTER TABLE user_missions CHANGE COLUMN `completed_at` `complèted_at` TIMESTAMP NULL');
            DB::statement('ALTER TABLE user_guided_tours CHANGE COLUMN `completed_at` `complèted_at` TIMESTAMP NULL');
            DB::statement('ALTER TABLE mission_completions CHANGE COLUMN `completed_at` `complèted_at` TIMESTAMP NULL');
        }
    }
};
