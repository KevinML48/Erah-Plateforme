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
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 20)->index();
            $table->unsignedInteger('points');
            $table->string('source_type', 100);
            $table->string('source_id', 191);
            $table->json('meta')->nullable();
            $table->unsignedInteger('before_xp')->default(0);
            $table->unsignedInteger('after_xp')->default(0);
            $table->unsignedInteger('before_rank_points')->default(0);
            $table->unsignedInteger('after_rank_points')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'kind', 'source_type', 'source_id'], 'points_idempotence_unique');
            $table->index(['user_id', 'kind', 'created_at']);
            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_transactions');
    }
};
