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
        Schema::create('match_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->string('idempotency_key', 120);
            $table->string('result', 20)->index();
            $table->unsignedInteger('bets_total')->default(0);
            $table->unsignedInteger('won_count')->default(0);
            $table->unsignedInteger('lost_count')->default(0);
            $table->unsignedInteger('void_count')->default(0);
            $table->unsignedInteger('payout_total')->default(0);
            $table->foreignId('processused_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processused_at')->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique('match_id', 'match_settlements_match_unique');
            $table->index(['processused_by', 'processused_at'], 'match_settlements_processused_by_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_settlements');
    }
};
