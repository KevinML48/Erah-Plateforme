<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_audit_logs', function (Blueprint $table): void {
            if (!Schema::hasColumn('admin_audit_logs', 'metadata_json')) {
                $table->json('metadata_json')->nullable()->after('entity_id');
            }

            if (!Schema::hasColumn('admin_audit_logs', 'ip')) {
                $table->string('ip', 64)->nullable()->after('metadata_json');
            }

            if (!Schema::hasColumn('admin_audit_logs', 'user_agent')) {
                $table->string('user_agent', 1024)->nullable()->after('ip');
            }

            if (Schema::hasColumn('admin_audit_logs', 'payload_json')) {
                $table->dropColumn('payload_json');
            }

            $table->index(['actor_user_id', 'created_at'], 'admin_audit_logs_actor_created_idx');
            $table->index(['action', 'created_at'], 'admin_audit_logs_action_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('admin_audit_logs', function (Blueprint $table): void {
            $table->dropIndex('admin_audit_logs_actor_created_idx');
            $table->dropIndex('admin_audit_logs_action_created_idx');

            if (!Schema::hasColumn('admin_audit_logs', 'payload_json')) {
                $table->json('payload_json')->nullable()->after('entity_id');
            }

            if (Schema::hasColumn('admin_audit_logs', 'metadata_json')) {
                $table->dropColumn('metadata_json');
            }

            if (Schema::hasColumn('admin_audit_logs', 'ip')) {
                $table->dropColumn('ip');
            }

            if (Schema::hasColumn('admin_audit_logs', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
        });
    }
};
