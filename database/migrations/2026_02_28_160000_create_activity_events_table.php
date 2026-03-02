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
        Schema::create('activity_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('event_type', 50)->index();
            $table->string('ref_type', 40)->index();
            $table->string('ref_id', 191)->index();
            $table->timestamp('occurred_at')->index();
            $table->string('unique_key', 191);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['user_id', 'occurred_at'], 'activity_events_user_occurred_idx');
            $table->index(['event_type', 'occurred_at'], 'activity_events_type_occurred_idx');
            $table->unique(['user_id', 'unique_key'], 'activity_events_user_unique_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_events');
    }
};
