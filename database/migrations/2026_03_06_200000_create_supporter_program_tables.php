<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supporter_plans', function (Blueprint $table) {
            $table->id();
            $table->string('key', 120)->unique();
            $table->string('name', 150);
            $table->unsignedInteger('price_cents');
            $table->string('currency', 8)->default('eur');
            $table->string('billing_interval', 20)->default('month');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('user_support_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supporter_plan_id')->constrained('supporter_plans')->cascadeOnDelete();
            $table->string('status', 32)->index();
            $table->string('provider', 32)->default('stripe')->index();
            $table->string('provider_customer_id', 191)->nullable()->index();
            $table->string('provider_subscription_id', 191)->nullable()->unique();
            $table->string('provider_price_id', 191)->nullable()->index();
            $table->string('checkout_session_id', 191)->nullable()->unique();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable()->index();
            $table->timestamp('canceled_at')->nullable()->index();
            $table->timestamp('ended_at')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status'], 'user_support_subscriptions_user_status_idx');
        });

        Schema::create('supporter_public_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->boolean('is_visible_on_wall')->default(true)->index();
            $table->string('display_name', 120)->nullable();
            $table->timestamps();
        });

        Schema::create('community_support_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('goal_count')->unique();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->boolean('is_unlocked')->default(false)->index();
            $table->timestamp('unlocked_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('supporter_monthly_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('reward_month')->index();
            $table->string('reward_key', 120);
            $table->timestamp('granted_at')->index();
            $table->timestamps();

            $table->unique(['user_id', 'reward_month', 'reward_key'], 'supporter_monthly_rewards_unique');
        });

        Schema::create('clip_vote_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->index();
            $table->string('title', 191);
            $table->timestamp('starts_at')->index();
            $table->timestamp('ends_at')->index();
            $table->string('status', 30)->index();
            $table->foreignId('winner_clip_id')->nullable()->constrained('clips')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('clip_vote_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('clip_vote_campaigns')->cascadeOnDelete();
            $table->foreignId('clip_id')->constrained('clips')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['campaign_id', 'clip_id'], 'clip_vote_entries_campaign_clip_unique');
        });

        Schema::create('clip_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('clip_vote_campaigns')->cascadeOnDelete();
            $table->foreignId('clip_id')->constrained('clips')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['campaign_id', 'user_id'], 'clip_votes_campaign_user_unique');
            $table->index(['campaign_id', 'clip_id'], 'clip_votes_campaign_clip_idx');
        });

        Schema::create('clip_supporter_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clip_id')->constrained('clips')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reaction_key', 60);
            $table->timestamps();

            $table->unique(['clip_id', 'user_id', 'reaction_key'], 'clip_supporter_reactions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clip_supporter_reactions');
        Schema::dropIfExists('clip_votes');
        Schema::dropIfExists('clip_vote_entries');
        Schema::dropIfExists('clip_vote_campaigns');
        Schema::dropIfExists('supporter_monthly_rewards');
        Schema::dropIfExists('community_support_goals');
        Schema::dropIfExists('supporter_public_profiles');
        Schema::dropIfExists('user_support_subscriptions');
        Schema::dropIfExists('supporter_plans');
    }
};
