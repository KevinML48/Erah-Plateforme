<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('prediction', 10);
            $table->boolean('is_correct')->nullable();
            $table->boolean('points_awarded')->default(false);
            $table->timestamps();

            $table->unique(['match_id', 'user_id']);
            $table->index('match_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
