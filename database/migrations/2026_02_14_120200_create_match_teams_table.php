<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_teams', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('side', 10);
            $table->timestamps();

            $table->unique(['match_id', 'team_id']);
            $table->unique(['match_id', 'side']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_teams');
    }
};

