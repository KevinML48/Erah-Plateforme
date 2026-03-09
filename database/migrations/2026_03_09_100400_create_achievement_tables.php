<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type', 40);
            $table->string('metric', 60);
            $table->unsignedInteger('threshold')->default(1);
            $table->string('badge_label', 60)->nullable();
            $table->json('rewards')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active', 'sort_order'], 'achievements_type_active_sort');
        });

        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('progress_value')->default(0);
            $table->json('meta')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();

            $table->unique(['achievement_id', 'user_id'], 'user_achievements_achievement_user_unique');
            $table->index(['user_id', 'unlocked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
    }
};
