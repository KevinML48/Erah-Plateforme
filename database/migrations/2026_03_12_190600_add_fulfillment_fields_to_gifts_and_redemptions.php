<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gifts', function (Blueprint $table): void {
            $table->boolean('is_featured')->default(false)->index()->after('is_active');
            $table->integer('sort_order')->default(0)->index()->after('is_featured');
        });

        Schema::table('gift_redemptions', function (Blueprint $table): void {
            $table->text('internal_note')->nullable()->after('shipping_note');
        });
    }

    public function down(): void
    {
        Schema::table('gift_redemptions', function (Blueprint $table): void {
            $table->dropColumn('internal_note');
        });

        Schema::table('gifts', function (Blueprint $table): void {
            $table->dropColumn(['is_featured', 'sort_order']);
        });
    }
};

