<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistant_favorites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('fingerprint', 64);
            $table->string('question', 500);
            $table->text('answer');
            $table->json('détails')->nullable();
            $table->json('sources')->nullable();
            $table->json('next_steps')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'fingerprint']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistant_favorites');
    }
};
