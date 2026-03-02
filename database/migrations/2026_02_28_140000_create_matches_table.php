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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('match_key', 80)->unique();
            $table->string('home_team', 120);
            $table->string('away_team', 120);
            $table->timestamp('starts_at')->index();
            $table->string('status', 20)->default('scheduled')->index();
            $table->string('result', 20)->nullable()->index();
            $table->timestamp('settled_at')->nullable()->index();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'starts_at'], 'matches_status_starts_idx');
            $table->index(['result', 'settled_at'], 'matches_result_settled_idx');
            $table->index(['created_by', 'created_at'], 'matches_created_by_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
