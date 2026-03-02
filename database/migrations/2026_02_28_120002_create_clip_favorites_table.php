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
        Schema::create('clip_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['clip_id', 'user_id'], 'clip_favorites_unique');
            $table->index(['clip_id', 'created_at'], 'clip_favorites_clip_created_idx');
            $table->index(['user_id', 'created_at'], 'clip_favorites_user_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clip_favorites');
    }
};
