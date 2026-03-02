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
        Schema::create('clip_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('channel', 30)->default('link')->index();
            $table->string('shared_url', 2048);
            $table->timestamps();

            $table->index(['clip_id', 'created_at'], 'clip_shares_clip_created_idx');
            $table->index(['user_id', 'created_at'], 'clip_shares_user_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clip_shares');
    }
};
