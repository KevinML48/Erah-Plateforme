<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supporter_plans', function (Blueprint $table): void {
            $table->string('stripe_price_id', 191)->nullable()->after('description')->index();
            $table->unsignedTinyInteger('billing_months')->default(1)->after('billing_interval');
            $table->decimal('discount_percent', 5, 2)->default(0)->after('billing_months');
            $table->unsignedSmallInteger('sort_order')->default(1)->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('supporter_plans', function (Blueprint $table): void {
            $table->dropColumn(['stripe_price_id', 'billing_months', 'discount_percent', 'sort_order']);
        });
    }
};
