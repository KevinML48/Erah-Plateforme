<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_reward_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('domain', 40);
            $table->string('action', 60);
            $table->string('dedupe_key')->unique();
            $table->string('subject_type', 80)->nullable();
            $table->string('subject_id', 100)->nullable();
            $table->integer('xp_amount')->default(0);
            $table->integer('rank_points_amount')->default(0);
            $table->integer('reward_points_amount')->default(0);
            $table->integer('bet_points_amount')->default(0);
            $table->integer('duel_score_amount')->default(0);
            $table->json('meta')->nullable();
            $table->date('granted_on');
            $table->timestamp('granted_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'domain', 'action', 'granted_on'], 'community_reward_grants_user_domain_action_day');
        });

        Schema::create('user_rank_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('league_key', 40);
            $table->string('league_name', 80);
            $table->unsignedInteger('xp_threshold')->default(0);
            $table->unsignedInteger('total_xp')->default(0);
            $table->json('meta')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'assigned_at']);
            $table->index(['league_key', 'total_xp']);
        });

        Schema::create('user_login_streaks', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('longest_streak')->default(0);
            $table->date('last_login_on')->nullable();
            $table->decimal('current_multiplier', 4, 2)->default(1.00);
            $table->integer('last_reward_points')->default(0);
            $table->timestamp('streak_started_at')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 40);
            $table->string('status', 20)->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active', 'starts_at', 'ends_at'], 'events_type_active_window');
        });

        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('endpoint_hash', 64)->unique();
            $table->text('endpoint');
            $table->text('public_key');
            $table->text('auth_token');
            $table->string('content_encoding', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('events');
        Schema::dropIfExists('user_login_streaks');
        Schema::dropIfExists('user_rank_histories');
        Schema::dropIfExists('community_reward_grants');
    }
};
