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
        Schema::create('clips', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->string('video_url', 2048);
            $table->string('thumbnail_url', 2048)->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_published', 'published_at'], 'clips_feed_recent_idx');
            $table->index(['is_published', 'likes_count', 'comments_count', 'published_at'], 'clips_feed_popular_idx');
            $table->index(['created_by', 'created_at'], 'clips_created_by_idx');
            $table->index(['updated_by', 'updated_at'], 'clips_updated_by_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clips');
    }
};
