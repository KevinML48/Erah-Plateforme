<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_selections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('market_id')->constrained('markets')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('market_options')->cascadeOnDelete();
            $table->decimal('odds_decimal_snapshot', 5, 2);
            $table->string('status', 20)->default('PENDING');
            $table->timestamps();

            $table->unique(['ticket_id', 'market_id']);
            $table->index(['ticket_id', 'status']);
            $table->index(['market_id', 'status']);
            $table->index(['option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_selections');
    }
};

