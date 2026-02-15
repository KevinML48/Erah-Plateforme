<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_progress', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('period_key', 32);
            $table->dateTime('completed_at')->nullable();
            $table->json('progress_json')->nullable();
            $table->boolean('awarded_points')->default(false);
            $table->dateTime('awarded_at')->nullable();
            $table->timestamps();

            $table->unique(['mission_id', 'user_id', 'period_key'], 'mission_progress_unique_period');
            $table->index(['user_id', 'mission_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('period_key');
            $table->index('awarded_points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_progress');
    }
};
