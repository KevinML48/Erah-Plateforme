<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->index('points_balance', 'users_points_balance_idx');
        });

        Schema::table('points_logs', function (Blueprint $table): void {
            $table->index('created_at', 'points_logs_created_at_idx');
            $table->index(['created_at', 'user_id', 'amount'], 'points_logs_created_user_amount_idx');
        });
    }

    public function down(): void
    {
        Schema::table('points_logs', function (Blueprint $table): void {
            $table->dropIndex('points_logs_created_user_amount_idx');
            $table->dropIndex('points_logs_created_at_idx');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_points_balance_idx');
        });
    }
};

