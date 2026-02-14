<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard_stats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('period', 20);
            $table->unsignedInteger('points_total')->default(0);
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->unique(['user_id', 'period'], 'leaderboard_stats_user_period_unique');
            $table->index(['period', 'points_total'], 'leaderboard_stats_period_points_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard_stats');
    }
};

