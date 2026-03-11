<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_guided_tours', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tour_key', 80);
            $table->string('status', 40);
            $table->unsignedInteger('current_step_index')->default(0);
            $table->boolean('is_paused')->default(true);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tour_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_guided_tours');
    }
};
