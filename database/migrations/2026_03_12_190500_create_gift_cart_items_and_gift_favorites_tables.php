<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_cart_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('gift_id')->constrained('gifts')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamp('added_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['user_id', 'gift_id'], 'gift_cart_items_user_gift_unique');
            $table->index(['user_id', 'updated_at'], 'gift_cart_items_user_updated_idx');
        });

        Schema::create('gift_favorites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('gift_id')->constrained('gifts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'gift_id'], 'gift_favorites_user_gift_unique');
            $table->index(['gift_id', 'created_at'], 'gift_favorites_gift_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_favorites');
        Schema::dropIfExists('gift_cart_items');
    }
};

