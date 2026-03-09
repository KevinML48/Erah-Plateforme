<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_items', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type', 40);
            $table->unsignedInteger('cost_points');
            $table->unsignedInteger('stock')->nullable();
            $table->json('payload')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'is_featured', 'sort_order'], 'shop_items_active_featured_sort');
        });

        Schema::create('user_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('cost_points');
            $table->string('status', 20)->default('completed');
            $table->json('payload')->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'purchased_at'], 'user_purchases_user_status_purchased');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_purchases');
        Schema::dropIfExists('shop_items');
    }
};
