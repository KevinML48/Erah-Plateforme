<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_progress', function (Blueprint $table) {
            $table->integer('duel_score')->default(0)->after('total_rank_points');
            $table->unsignedInteger('duel_wins')->default(0)->after('duel_score');
            $table->unsignedInteger('duel_losses')->default(0)->after('duel_wins');
        });

        Schema::create('duel_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('winner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('loser_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('challenger_score')->nullable();
            $table->integer('challenged_score')->nullable();
            $table->text('note')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->unique('duel_id');
            $table->index(['winner_user_id', 'settled_at']);
            $table->index(['loser_user_id', 'settled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duel_results');

        Schema::table('user_progress', function (Blueprint $table) {
            $table->dropColumn(['duel_score', 'duel_wins', 'duel_losses']);
        });
    }
};
