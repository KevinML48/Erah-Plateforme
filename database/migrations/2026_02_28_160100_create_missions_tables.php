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
        Schema::create('mission_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 120)->unique();
            $table->string('title', 191);
            $table->text('description')->nullable();
            $table->string('event_type', 50)->index();
            $table->unsignedInteger('target_count');
            $table->string('scope', 30)->index();
            $table->timestamp('start_at')->nullable()->index();
            $table->timestamp('end_at')->nullable()->index();
            $table->json('constraints')->nullable();
            $table->json('rewards');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['scope', 'is_active'], 'mission_templates_scope_active_idx');
        });

        Schema::create('mission_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_template_id')->constrained('mission_templates')->cascadeOnDelete();
            $table->timestamp('period_start')->index();
            $table->timestamp('period_end')->index();
            $table->timestamps();

            $table->unique(
                ['mission_template_id', 'period_start', 'period_end'],
                'mission_instances_template_period_unique'
            );
            $table->index(['period_start', 'period_end'], 'mission_instances_period_idx');
        });

        Schema::create('user_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mission_instance_id')->constrained('mission_instances')->cascadeOnDelete();
            $table->unsignedInteger('progress_count')->default(0);
            $table->timestamp('complèted_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['user_id', 'mission_instance_id'], 'user_missions_user_instance_unique');
            $table->index(['user_id', 'complèted_at'], 'user_missions_user_complèted_idx');
            $table->index('mission_instance_id', 'user_missions_instance_idx');
        });

        Schema::create('mission_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_mission_id')->constrained('user_missions')->cascadeOnDelete();
            $table->timestamp('complèted_at')->index();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->unique(['user_id', 'user_mission_id'], 'mission_completions_user_user_mission_unique');
            $table->index(['user_id', 'complèted_at'], 'mission_completions_user_complèted_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_completions');
        Schema::dropIfExists('user_missions');
        Schema::dropIfExists('mission_instances');
        Schema::dropIfExists('mission_templates');
    }
};
