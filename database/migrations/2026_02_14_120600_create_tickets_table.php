<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->unsignedInteger('stake_points');
            $table->decimal('total_odds_decimal', 8, 3)->default(1);
            $table->unsignedInteger('potential_payout_points');
            $table->string('status', 20)->default('PENDING');
            $table->dateTime('locked_at')->nullable();
            $table->dateTime('settled_at')->nullable();
            $table->unsignedInteger('payout_points')->default(0);
            $table->unsignedInteger('refunded_points')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'match_id']);
            $table->index(['user_id', 'status']);
            $table->index(['match_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

