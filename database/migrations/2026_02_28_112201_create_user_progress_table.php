<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->foreignId('current_league_id')->nullable()->constrained('leagues')->nullOnDelete();
            $table->unsignedInteger('total_xp')->default(0)->index();
            $table->unsignedInteger('total_rank_points')->default(0)->index();
            $table->timestamp('last_points_at')->nullable()->index();
            $table->timestamps();

            $table->index(['current_league_id', 'total_rank_points']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
