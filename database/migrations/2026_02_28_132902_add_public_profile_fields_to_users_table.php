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
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('role');
            $table->string('avatar_path', 2048)->nullable()->after('bio');
            $table->string('twitter_url', 255)->nullable()->after('avatar_path');
            $table->string('instagram_url', 255)->nullable()->after('twitter_url');
            $table->string('tiktok_url', 255)->nullable()->after('instagram_url');
            $table->string('discord_url', 255)->nullable()->after('tiktok_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'avatar_path',
                'twitter_url',
                'instagram_url',
                'tiktok_url',
                'discord_url',
            ]);
        });
    }
};
