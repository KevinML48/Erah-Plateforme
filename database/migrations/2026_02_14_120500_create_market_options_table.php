<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('market_id')->constrained('markets')->cascadeOnDelete();
            $table->string('label');
            $table->string('key', 100);
            $table->decimal('odds_decimal', 5, 2)->default(1.50);
            $table->decimal('popularity_weight', 6, 4)->nullable();
            $table->boolean('is_winner')->nullable();
            $table->dateTime('settled_at')->nullable();
            $table->timestamps();

            $table->index(['market_id', 'key']);
            $table->unique(['market_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_options');
    }
};

