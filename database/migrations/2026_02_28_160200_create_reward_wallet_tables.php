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
        Schema::create('user_reward_wallets', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->integer('balance')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('reward_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 30)->index();
            $table->integer('amount');
            $table->integer('balance_after');
            $table->string('ref_type', 40)->nullable()->index();
            $table->string('ref_id', 191)->nullable()->index();
            $table->string('unique_key', 191);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->unique(['user_id', 'unique_key'], 'reward_wallet_transactions_user_unique_key_unique');
            $table->index(['user_id', 'created_at'], 'reward_wallet_transactions_user_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_wallet_transactions');
        Schema::dropIfExists('user_reward_wallets');
    }
};
