<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistant_conversations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 160);
            $table->string('provider', 60)->nullable();
            $table->string('model', 120)->nullable();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();

            $table->index(['user_id', 'last_message_at']);
        });

        Schema::create('assistant_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('assistant_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20);
            $table->longText('content');
            $table->string('provider', 60)->nullable();
            $table->string('model', 120)->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['assistant_conversation_id', 'created_at']);
            $table->index(['assistant_conversation_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistant_messages');
        Schema::dropIfExists('assistant_conversations');
    }
};
