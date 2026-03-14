<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gifts', function (Blueprint $table) {
            $table->string('key', 120)->nullable()->after('id')->unique('gifts_key_unique');
            $table->string('category', 40)->nullable()->after('description')->index('gifts_category_idx');
            $table->string('type', 60)->nullable()->after('category');
            $table->string('delivery_type', 40)->nullable()->after('type')->index('gifts_delivery_type_idx');
            $table->boolean('requires_admin_validation')->default(false)->after('delivery_type');
            $table->json('metadata')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('gifts', function (Blueprint $table) {
            $table->dropUnique('gifts_key_unique');
            $table->dropIndex('gifts_category_idx');
            $table->dropIndex('gifts_delivery_type_idx');
            $table->dropColumn([
                'key',
                'category',
                'type',
                'delivery_type',
                'requires_admin_validation',
                'metadata',
            ]);
        });
    }
};
