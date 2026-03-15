<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('event_type', 32)->default('head_to_head')->after('game_key');
            $table->string('event_name', 160)->nullable()->after('event_type');
            $table->string('compétition_name', 160)->nullable()->after('event_name');
            $table->string('compétition_stage', 120)->nullable()->after('compétition_name');
            $table->string('compétition_split', 120)->nullable()->after('compétition_stage');
            $table->unsignedTinyInteger('best_of')->nullable()->after('compétition_split');
            $table->foreignId('parent_match_id')->nullable()->after('best_of')->constrained('matches')->nullOnDelete();
            $table->timestamp('ends_at')->nullable()->after('locked_at');
            $table->timestamp('child_matches_unlocked_at')->nullable()->after('parent_match_id');
            $table->unsignedTinyInteger('team_a_score')->nullable()->after('finished_at');
            $table->unsignedTinyInteger('team_b_score')->nullable()->after('team_a_score');

            $table->index('event_type');
            $table->index('parent_match_id');
            $table->index('ends_at');
            $table->index('child_matches_unlocked_at');
            $table->index(['game_key', 'event_type', 'starts_at'], 'matches_game_event_starts_idx');
        });

        Schema::table('match_selections', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('odds');
            $table->index(['market_id', 'sort_order'], 'match_selections_market_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::table('match_selections', function (Blueprint $table) {
            $table->dropIndex('match_selections_market_sort_idx');
            $table->dropColumn('sort_order');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex('matches_game_event_starts_idx');
            $table->dropIndex(['event_type']);
            $table->dropIndex(['parent_match_id']);
            $table->dropIndex(['ends_at']);
            $table->dropIndex(['child_matches_unlocked_at']);
            $table->dropConstrainedForeignId('parent_match_id');
            $table->dropColumn([
                'event_type',
                'event_name',
                'compétition_name',
                'compétition_stage',
                'compétition_split',
                'best_of',
                'ends_at',
                'child_matches_unlocked_at',
                'team_a_score',
                'team_b_score',
            ]);
        });
    }
};
