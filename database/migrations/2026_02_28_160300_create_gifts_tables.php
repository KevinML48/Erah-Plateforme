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
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191);
            $table->text('description');
            $table->string('image_url', 2048)->nullable();
            $table->unsignedInteger('cost_points')->index();
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['is_active', 'cost_points'], 'gifts_active_cost_idx');
        });

        Schema::create('gift_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('gift_id')->constrained('gifts')->restrictOnDelete();
            $table->unsignedInteger('cost_points_snapshot');
            $table->string('status', 20)->default('pending')->index();
            $table->text('reason')->nullable();
            $table->string('tracking_code', 191)->nullable();
            $table->timestamp('requested_at')->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status'], 'gift_redemptions_user_status_idx');
            $table->index(['status', 'requested_at'], 'gift_redemptions_status_requested_idx');
            $table->index(['gift_id', 'status'], 'gift_redemptions_gift_status_idx');
        });

        Schema::create('gift_redemption_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('redemption_id')->constrained('gift_redemptions')->cascadeOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 40)->index();
            $table->json('data')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['redemption_id', 'created_at'], 'gift_redemption_events_redemption_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_redemption_events');
        Schema::dropIfExists('gift_redemptions');
        Schema::dropIfExists('gifts');
    }
};
