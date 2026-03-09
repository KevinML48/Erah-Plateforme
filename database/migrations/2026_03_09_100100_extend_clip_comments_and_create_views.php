<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clip_comments', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('clip_id')->constrained('clip_comments')->nullOnDelete();
            $table->string('status', 20)->default('published')->after('body');
            $table->timestamp('moderated_at')->nullable()->after('status');
            $table->index(['clip_id', 'parent_id', 'created_at'], 'clip_comments_tree_index');
        });

        Schema::create('clip_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 120)->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            $table->index(['clip_id', 'user_id', 'created_at'], 'clip_views_clip_user_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clip_views');

        Schema::table('clip_comments', function (Blueprint $table) {
            $table->dropIndex('clip_comments_tree_index');
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['status', 'moderated_at']);
        });
    }
};
