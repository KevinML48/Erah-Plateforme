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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('platform', 30)->index();
            $table->string('device_token', 255);
            $table->string('device_name', 120)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->json('meta')->nullable();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['user_id', 'platform', 'device_token'], 'user_devices_unique');
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
