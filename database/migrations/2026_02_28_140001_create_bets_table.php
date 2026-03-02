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
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->string('prediction', 20)->index();
            $table->unsignedInteger('stake_points');
            $table->unsignedInteger('potential_payout');
            $table->unsignedInteger('settlement_points')->default(0);
            $table->string('status', 20)->default('pending')->index();
            $table->string('idempotency_key', 120);
            $table->timestamp('placed_at')->index();
            $table->timestamp('settled_at')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'idempotency_key'], 'bets_user_idempotency_unique');
            $table->unique(['user_id', 'match_id'], 'bets_user_match_unique');
            $table->index(['match_id', 'status'], 'bets_match_status_idx');
            $table->index(['user_id', 'status'], 'bets_user_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
