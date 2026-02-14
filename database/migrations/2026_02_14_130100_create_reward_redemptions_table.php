<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reward_id')->constrained('rewards')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('PENDING');
            $table->unsignedInteger('points_cost_snapshot');
            $table->string('reward_name_snapshot');
            $table->string('shipping_name')->nullable();
            $table->string('shipping_email')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_address1')->nullable();
            $table->string('shipping_address2')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_country')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('tracking_code')->nullable();
            $table->boolean('debited_points')->default(false);
            $table->boolean('refunded_points')->default(false);
            $table->boolean('reserved_stock')->default(false);
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['reward_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_redemptions');
    }
};

