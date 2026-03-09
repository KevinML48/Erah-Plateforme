<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft');
            $table->integer('reward_points')->default(0);
            $table->integer('bet_points')->default(0);
            $table->integer('xp_reward')->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_user_limit')->default(1);
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('mission_template_id')->nullable()->constrained('mission_templates')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
        });

        Schema::create('live_code_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_code_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('reward_points')->default(0);
            $table->integer('bet_points')->default(0);
            $table->integer('xp_reward')->default(0);
            $table->json('meta')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();

            $table->unique(['live_code_id', 'user_id'], 'live_code_redemptions_code_user_unique');
            $table->index(['user_id', 'redeemed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_code_redemptions');
        Schema::dropIfExists('live_codes');
    }
};
