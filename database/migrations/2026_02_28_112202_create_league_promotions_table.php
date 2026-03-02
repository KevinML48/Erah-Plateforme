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
        Schema::create('league_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_league_id')->nullable()->constrained('leagues')->nullOnDelete();
            $table->foreignId('to_league_id')->constrained('leagues')->cascadeOnDelete();
            $table->foreignId('points_transaction_id')->constrained('points_transactions')->cascadeOnDelete();
            $table->unsignedInteger('rank_points');
            $table->timestamp('promoted_at')->useCurrent()->index();
            $table->timestamps();

            $table->index(['user_id', 'promoted_at']);
            $table->unique(['user_id', 'to_league_id', 'points_transaction_id'], 'league_promotions_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_promotions');
    }
};
