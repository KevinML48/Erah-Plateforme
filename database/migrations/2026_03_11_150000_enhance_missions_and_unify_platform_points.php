<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mission_templates', function (Blueprint $table) {
            $table->string('short_description', 280)->nullable()->after('title');
            $table->text('long_description')->nullable()->after('description');
            $table->string('category', 60)->nullable()->after('scope')->index();
            $table->string('type', 40)->nullable()->after('category')->index();
            $table->string('difficulty', 40)->nullable()->after('type')->index();
            $table->unsignedSmallInteger('estimated_minutes')->nullable()->after('difficulty');
            $table->boolean('is_discovery')->default(false)->after('estimated_minutes')->index();
            $table->boolean('is_featured')->default(false)->after('is_discovery')->index();
            $table->boolean('is_repeatable')->default(false)->after('is_featured');
            $table->boolean('requires_claim')->default(false)->after('is_repeatable');
            $table->unsignedInteger('sort_order')->default(0)->after('requires_claim')->index();
            $table->json('prerequisites')->nullable()->after('sort_order');
            $table->string('icon', 120)->nullable()->after('prerequisites');
            $table->string('badge_label', 80)->nullable()->after('icon');
            $table->json('ui_meta')->nullable()->after('badge_label');
        });

        Schema::table('user_missions', function (Blueprint $table) {
            $table->timestamp('rewarded_at')->nullable()->after('completed_at')->index();
            $table->timestamp('claimed_at')->nullable()->after('rewarded_at')->index();
            $table->timestamp('expired_at')->nullable()->after('claimed_at')->index();
            $table->timestamp('last_tracked_at')->nullable()->after('expired_at');
        });

        Schema::create('mission_event_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('event_key', 191);
            $table->string('event_type', 80)->index();
            $table->string('subject_type', 120)->nullable();
            $table->string('subject_id', 120)->nullable();
            $table->unsignedInteger('amount')->default(1);
            $table->json('context')->nullable();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->timestamp('processused_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['user_id', 'event_key'], 'mission_event_records_user_event_unique');
            $table->index(['user_id', 'event_type'], 'mission_event_records_user_type_idx');
        });

        Schema::create('user_mission_focuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mission_template_id')->constrained('mission_templates')->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'mission_template_id'], 'user_mission_focuses_user_template_unique');
            $table->index(['user_id', 'sort_order'], 'user_mission_focuses_user_sort_idx');
        });

        $this->backfillMissionTemplates();
        $this->backfillUserMissions();
        $this->unifyExistingPointBalances();
    }

    public function down(): void
    {
        Schema::dropIfExists('user_mission_focuses');
        Schema::dropIfExists('mission_event_records');

        Schema::table('user_missions', function (Blueprint $table) {
            $table->dropColumn([
                'rewarded_at',
                'claimed_at',
                'expired_at',
                'last_tracked_at',
            ]);
        });

        Schema::table('mission_templates', function (Blueprint $table) {
            $table->dropColumn([
                'short_description',
                'long_description',
                'category',
                'type',
                'difficulty',
                'estimated_minutes',
                'is_discovery',
                'is_featured',
                'is_repeatable',
                'requires_claim',
                'sort_order',
                'prerequisites',
                'icon',
                'badge_label',
                'ui_meta',
            ]);
        });
    }

    private function backfillMissionTemplates(): void
    {
        $templates = DB::table('mission_templates')->get();

        foreach ($templates as $template) {
            $constraints = $this->decodeJsonPayload($template->constraints);
            $rewards = $this->decodeJsonPayload($template->rewards);

            $xp = (int) ($rewards['xp'] ?? $rewards['xp_amount'] ?? 0);
            $points = (int) (
                $rewards['points']
                ?? $rewards['reward_points']
                ?? $rewards['reward_points_amount']
                ?? 0
            );
            $points += (int) ($rewards['bet_points'] ?? $rewards['bet_points_amount'] ?? 0);

            DB::table('mission_templates')
                ->where('id', $template->id)
                ->update([
                    'event_type' => (string) str((string) $template->event_type)->trim()->lower()->replace([' ', '-', '_'], '.')->replace('..', '.')->trim('.'),
                    'short_description' => $template->short_description ?? $template->description,
                    'long_description' => $template->long_description ?? $template->description,
                    'category' => $template->category ?? $this->resolveCategoryFromEventType((string) $template->event_type),
                    'type' => $template->type ?? $this->resolveTypeFromScope((string) $template->scope),
                    'difficulty' => $template->difficulty ?? ($constraints['difficulty'] ?? null),
                    'estimated_minutes' => $template->estimated_minutes ?? $this->resolveEstimatedMinutes((string) $template->scope, (int) $template->target_count),
                    'is_discovery' => (bool) ($template->is_discovery ?? ($constraints['is_discovery'] ?? false)),
                    'is_featured' => (bool) ($template->is_featured ?? ($constraints['is_featured'] ?? false)),
                    'is_repeatable' => (bool) ($template->is_repeatable ?? in_array($template->scope, ['daily', 'weekly', 'monthly'], true)),
                    'requires_claim' => (bool) ($template->requires_claim ?? false),
                    'sort_order' => (int) ($template->sort_order ?? 0),
                    'icon' => $template->icon ?? ($constraints['icon'] ?? null),
                    'badge_label' => $template->badge_label ?? ($constraints['badge_label'] ?? null),
                    'prerequisites' => $template->prerequisites ?? $this->encodeJsonPayload($constraints['prerequisites'] ?? null),
                    'ui_meta' => $template->ui_meta ?? $this->encodeJsonPayload($constraints['ui'] ?? null),
                    'rewards' => $this->encodeJsonPayload([
                        'xp' => max(0, $xp),
                        'points' => max(0, $points),
                    ]),
                ]);
        }
    }

    private function backfillUserMissions(): void
    {
        DB::table('user_missions')
            ->whereNotNull('completed_at')
            ->whereNull('rewarded_at')
            ->update([
                'rewarded_at' => DB::raw('completed_at'),
                'claimed_at' => DB::raw('completed_at'),
            ]);

        $now = now();

        $activeMissionIds = DB::table('mission_instances')
            ->where('period_end', '<', $now)
            ->pluck('id');

        if ($activeMissionIds->isEmpty()) {
            return;
        }

        DB::table('user_missions')
            ->whereIn('mission_instance_id', $activeMissionIds->all())
            ->whereNull('completed_at')
            ->whereNull('expired_at')
            ->update([
                'expired_at' => $now,
            ]);
    }

    private function unifyExistingPointBalances(): void
    {
        if (! Schema::hasTable('user_reward_wallets') || ! Schema::hasTable('user_wallets')) {
            return;
        }

        $users = DB::table('users')->pluck('id');

        foreach ($users as $userId) {
            $rewardBalance = (int) (DB::table('user_reward_wallets')->where('user_id', $userId)->value('balance') ?? 0);
            $betBalance = (int) (DB::table('user_wallets')->where('user_id', $userId)->value('balance') ?? 0);
            $mergedBalance = $rewardBalance + $betBalance;

            DB::table('user_reward_wallets')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'balance' => $mergedBalance,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );

            DB::table('user_wallets')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'balance' => $mergedBalance,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );

            $delta = $mergedBalance - $rewardBalance;
            if ($delta <= 0) {
                continue;
            }

            $uniqueKey = 'wallet.unification.bootstrap.user.'.$userId;
            $exists = DB::table('reward_wallet_transactions')
                ->where('user_id', $userId)
                ->where('unique_key', $uniqueKey)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('reward_wallet_transactions')->insert([
                'user_id' => $userId,
                'type' => 'admin_adjustment',
                'amount' => $delta,
                'balance_after' => $mergedBalance,
                'ref_type' => 'system',
                'ref_id' => 'wallet-unification',
                'unique_key' => $uniqueKey,
                'metadata' => json_encode([
                    'reason' => 'wallet_unification',
                    'legacy_reward_balance' => $rewardBalance,
                    'legacy_bet_balance' => $betBalance,
                ], JSON_THROW_ON_ERROR),
                'created_at' => now(),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonPayload(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function encodeJsonPayload(mixed $value): ?string
    {
        if ($value === null || $value === []) {
            return null;
        }

        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    private function resolveCategoryFromEventType(string $eventType): string
    {
        return (string) str($eventType)->before('.')->replace('_', '-')->value();
    }

    private function resolveTypeFromScope(string $scope): string
    {
        return match ($scope) {
            'once' => 'core',
            'event_window' => 'event',
            default => 'repeatable',
        };
    }

    private function resolveEstimatedMinutes(string $scope, int $targetCount): int
    {
        return match ($scope) {
            'daily' => max(5, min(30, $targetCount * 4)),
            'weekly' => max(15, min(90, $targetCount * 8)),
            'event_window' => max(20, min(120, $targetCount * 10)),
            default => max(5, min(60, $targetCount * 5)),
        };
    }
};
