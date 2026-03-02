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
        Schema::create('duels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenger_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('challenged_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('pending')->index();
            $table->string('idempotency_key', 120);
            $table->text('message')->nullable();
            $table->timestamp('requested_at')->index();
            $table->timestamp('expires_at')->index();
            $table->timestamp('responded_at')->nullable()->index();
            $table->timestamp('accepted_at')->nullable()->index();
            $table->timestamp('refused_at')->nullable()->index();
            $table->timestamp('expired_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['challenger_id', 'idempotency_key'], 'duels_idempotency_unique');
            $table->index(['challenger_id', 'status', 'created_at'], 'duels_challenger_status_idx');
            $table->index(['challenged_id', 'status', 'created_at'], 'duels_challenged_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duels');
    }
};
