<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('equipped_profile_badge', 120)->nullable()->after('discord_url');
            $table->string('equipped_avatar_frame', 120)->nullable()->after('equipped_profile_badge');
            $table->string('equipped_profile_banner', 120)->nullable()->after('equipped_avatar_frame');
            $table->string('equipped_profile_title', 120)->nullable()->after('equipped_profile_banner');
            $table->string('equipped_username_color', 120)->nullable()->after('equipped_profile_title');
            $table->string('equipped_profile_theme', 120)->nullable()->after('equipped_username_color');
            $table->timestamp('profile_featured_until')->nullable()->after('equipped_profile_theme');
        });

        Schema::create('user_profile_cosmetics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('gift_id')->nullable()->constrained('gifts')->nullOnDelete();
            $table->foreignId('gift_redemption_id')->nullable()->constrained('gift_redemptions')->nullOnDelete();
            $table->string('slot', 40)->index();
            $table->string('cosmetic_key', 120);
            $table->timestamp('expires_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'cosmetic_key'], 'user_profile_cosmetics_user_key_unique');
            $table->index(['user_id', 'slot'], 'user_profile_cosmetics_user_slot_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profile_cosmetics');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'equipped_profile_badge',
                'equipped_avatar_frame',
                'equipped_profile_banner',
                'equipped_profile_title',
                'equipped_username_color',
                'equipped_profile_theme',
                'profile_featured_until',
            ]);
        });
    }
};
