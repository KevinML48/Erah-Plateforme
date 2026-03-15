<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename complèted_at to completed_at in user_missions table
        if (Schema::hasColumn('user_missions', 'complèted_at')) {
            Schema::table('user_missions', function (Blueprint $table) {
                $table->renameColumn('complèted_at', 'completed_at');
            });
        }

        // Rename complèted_at to completed_at in mission_completions table
        if (Schema::hasColumn('mission_completions', 'complèted_at')) {
            Schema::table('mission_completions', function (Blueprint $table) {
                $table->renameColumn('complèted_at', 'completed_at');
            });
        }

        // Rename détails to details in assistant_favorites table
        if (Schema::hasColumn('assistant_favorites', 'détails')) {
            Schema::table('assistant_favorites', function (Blueprint $table) {
                $table->renameColumn('détails', 'details');
            });
        }

        // Rename complèted_at to completed_at in user_guided_tours table
        if (Schema::hasColumn('user_guided_tours', 'complèted_at')) {
            Schema::table('user_guided_tours', function (Blueprint $table) {
                $table->renameColumn('complèted_at', 'completed_at');
            });
        }

        // Fix status column default in purchases table (change complèted to completed)
        if (Schema::hasColumn('purchases', 'status')) {
            DB::statement("ALTER TABLE `purchases` MODIFY `status` VARCHAR(50) DEFAULT 'completed'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: Rename completed_at back to complèted_at in user_missions table
        if (Schema::hasColumn('user_missions', 'completed_at')) {
            Schema::table('user_missions', function (Blueprint $table) {
                $table->renameColumn('completed_at', 'complèted_at');
            });
        }

        // Rollback: Rename completed_at back to complèted_at in mission_completions table
        if (Schema::hasColumn('mission_completions', 'completed_at')) {
            Schema::table('mission_completions', function (Blueprint $table) {
                $table->renameColumn('completed_at', 'complèted_at');
            });
        }

        // Rollback: Rename details back to détails in assistant_favorites table
        if (Schema::hasColumn('assistant_favorites', 'details')) {
            Schema::table('assistant_favorites', function (Blueprint $table) {
                $table->renameColumn('details', 'détails');
            });
        }

        // Rollback: Rename completed_at back to complèted_at in user_guided_tours table
        if (Schema::hasColumn('user_guided_tours', 'completed_at')) {
            Schema::table('user_guided_tours', function (Blueprint $table) {
                $table->renameColumn('completed_at', 'complèted_at');
            });
        }

        // Rollback: Fix status column default in purchases table back to complèted
        if (Schema::hasColumn('purchases', 'status')) {
            DB::statement("ALTER TABLE `purchases` MODIFY `status` VARCHAR(50) DEFAULT 'complèted'");
        }
    }
};
