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
        Schema::create('duel_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type', 40)->index();
            $table->json('meta')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['duel_id', 'occurred_at'], 'duel_events_duel_occurred_idx');
            $table->index(['actor_id', 'occurred_at'], 'duel_events_actor_occurred_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duel_events');
    }
};
