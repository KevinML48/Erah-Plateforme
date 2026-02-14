<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->string('code', 100);
            $table->string('name');
            $table->string('status', 20)->default('OPEN');
            $table->json('settle_rule')->nullable();
            $table->dateTime('settled_at')->nullable();
            $table->timestamps();

            $table->index(['match_id', 'status']);
            $table->index(['match_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};

