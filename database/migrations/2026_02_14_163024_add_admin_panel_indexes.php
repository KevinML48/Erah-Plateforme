<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reward_redemptions', function (Blueprint $table): void {
            $table->index(['status', 'user_id', 'reward_id'], 'reward_redemptions_status_user_reward_idx');
        });

        Schema::table('matches', function (Blueprint $table): void {
            $table->index(['starts_at', 'status'], 'matches_starts_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table): void {
            $table->dropIndex('matches_starts_status_idx');
        });

        Schema::table('reward_redemptions', function (Blueprint $table): void {
            $table->dropIndex('reward_redemptions_status_user_reward_idx');
        });
    }
};
