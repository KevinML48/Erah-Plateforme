<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_videos', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 320)->nullable();
            $table->text('description')->nullable();
            $table->string('platform', 32)->default('youtube');
            $table->string('video_url', 2048);
            $table->string('embed_url', 2048)->nullable();
            $table->string('thumbnail_url', 2048)->nullable();
            $table->string('preview_video_url', 2048)->nullable();
            $table->string('preview_video_webm_url', 2048)->nullable();
            $table->string('category_key', 64)->nullable();
            $table->string('category_label', 120)->nullable();
            $table->string('status', 16)->default('draft');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('legacy_source')->nullable();
            $table->string('imported_hash', 64)->nullable()->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['category_key', 'sort_order']);
            $table->index(['is_featured', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_videos');
    }
};