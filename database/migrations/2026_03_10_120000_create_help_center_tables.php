<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('help_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 160);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->text('intro')->nullable();
            $table->string('icon', 60)->nullable();
            $table->string('landing_bucket', 60)->default('understanding_platform')->index();
            $table->string('tutorial_video_url')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('help_articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('help_category_id')->constrained('help_categories')->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->text('summary')->nullable();
            $table->longText('body');
            $table->text('short_answer')->nullable();
            $table->json('keywords')->nullable();
            $table->string('tutorial_video_url')->nullable();
            $table->string('cta_label', 120)->nullable();
            $table->string('cta_url')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_faq')->default(false)->index();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('help_glossary_terms', function (Blueprint $table): void {
            $table->id();
            $table->string('term', 140);
            $table->string('slug', 180)->unique();
            $table->text('definition');
            $table->text('short_answer')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->string('status', 20)->default('draft')->index();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('help_tour_steps', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('step_number')->unique();
            $table->string('title', 180);
            $table->text('summary');
            $table->text('body');
            $table->string('visual_title', 180)->nullable();
            $table->text('visual_body')->nullable();
            $table->string('cta_label', 120);
            $table->string('cta_url');
            $table->string('tutorial_video_url')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('help_tour_steps');
        Schema::dropIfExists('help_glossary_terms');
        Schema::dropIfExists('help_articles');
        Schema::dropIfExists('help_categories');
    }
};
