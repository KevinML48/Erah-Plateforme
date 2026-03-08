<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author_name')->nullable();
            $table->string('author_profile_url')->nullable();
            $table->text('content');
            $table->string('status', 24)->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->string('source', 24)->default('member');
            $table->unsignedInteger('display_order')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['status', 'published_at']);
            $table->index(['source', 'status']);
            $table->index('display_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_reviews');
    }
};
