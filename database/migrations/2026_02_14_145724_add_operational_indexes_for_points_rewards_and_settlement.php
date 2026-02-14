<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('points_logs', function (Blueprint $table): void {
            $table->index(['type', 'created_at'], 'points_logs_type_created_at_idx');
            $table->index(['user_id', 'type', 'created_at'], 'points_logs_user_type_created_idx');
        });

        Schema::table('predictions', function (Blueprint $table): void {
            $table->index(['match_id', 'is_correct', 'points_awarded'], 'predictions_match_award_idx');
        });

        Schema::table('tickets', function (Blueprint $table): void {
            $table->index(['status', 'settled_at'], 'tickets_status_settled_idx');
            $table->index(['match_id', 'status', 'settled_at'], 'tickets_match_status_settled_idx');
        });

        Schema::table('ticket_selections', function (Blueprint $table): void {
            $table->index(['market_id', 'status', 'ticket_id'], 'ticket_selections_market_status_ticket_idx');
        });

        Schema::table('reward_redemptions', function (Blueprint $table): void {
            $table->index(['status', 'refunded_points'], 'reward_redemptions_status_refunded_idx');
            $table->index(['status', 'reserved_stock'], 'reward_redemptions_status_reserved_idx');
        });
    }

    public function down(): void
    {
        Schema::table('reward_redemptions', function (Blueprint $table): void {
            $table->dropIndex('reward_redemptions_status_refunded_idx');
            $table->dropIndex('reward_redemptions_status_reserved_idx');
        });

        Schema::table('ticket_selections', function (Blueprint $table): void {
            $table->dropIndex('ticket_selections_market_status_ticket_idx');
        });

        Schema::table('tickets', function (Blueprint $table): void {
            $table->dropIndex('tickets_status_settled_idx');
            $table->dropIndex('tickets_match_status_settled_idx');
        });

        Schema::table('predictions', function (Blueprint $table): void {
            $table->dropIndex('predictions_match_award_idx');
        });

        Schema::table('points_logs', function (Blueprint $table): void {
            $table->dropIndex('points_logs_type_created_at_idx');
            $table->dropIndex('points_logs_user_type_created_idx');
        });
    }
};
