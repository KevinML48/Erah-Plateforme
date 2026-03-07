<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_photos', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('video_path')->nullable();
            $table->string('media_type', 16)->default('image');
            $table->string('alt_text')->nullable();
            $table->string('filter_key', 64)->nullable();
            $table->string('filter_label', 120)->nullable();
            $table->string('category_label', 120)->nullable();
            $table->string('cursor_label', 120)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->string('storage_disk', 64)->nullable();
            $table->string('media_mime_type', 191)->nullable();
            $table->unsignedBigInteger('media_size')->nullable();
            $table->string('legacy_source')->nullable();
            $table->string('imported_hash', 64)->nullable()->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'published_at']);
            $table->index(['filter_key', 'sort_order']);
            $table->index(['media_type', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_photos');
    }
};
