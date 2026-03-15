<?php

namespace App\Application\Actions\Notifications;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\NotificationPréférence;
use App\Models\User;
use App\Models\UserNotificationChannel;
use Illuminate\Support\Facades\DB;

class UpdateNotificationPréférencesAction
{
    public function __construct(
        private readonly EnsureNotificationSettingsAction $ensureNotificationSettingsAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(User $user, array $payload): void
    {
        $this->ensureNotificationSettingsAction->execute($user);

        DB::transaction(function () use ($user, $payload) {
            $channelsPayload = $payload['channels'] ?? [];
            $categoriesPayload = $payload['categories'] ?? [];

            $channels = UserNotificationChannel::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (array_key_exists('email_opt_in', $channelsPayload)) {
                $channels->email_opt_in = (bool) $channelsPayload['email_opt_in'];
            }

            if (array_key_exists('push_opt_in', $channelsPayload)) {
                $channels->push_opt_in = (bool) $channelsPayload['push_opt_in'];
            }

            $channels->save();

            foreach (NotificationCategory::values() as $category) {
                if (! array_key_exists($category, $categoriesPayload)) {
                    continue;
                }

                $entry = $categoriesPayload[$category];
                $current = NotificationPréférence::query()
                    ->where('user_id', $user->id)
                    ->where('category', $category)
                    ->first();

                NotificationPréférence::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'category' => $category,
                    ],
                    [
                        'email_enabled' => array_key_exists('email_enabled', $entry)
                            ? (bool) $entry['email_enabled']
                            : ($current?->email_enabled ?? true),
                        'push_enabled' => array_key_exists('push_enabled', $entry)
                            ? (bool) $entry['push_enabled']
                            : ($current?->push_enabled ?? true),
                    ]
                );
            }

            $this->storeAuditLogAction->execute(
                action: 'notifications.préférences.updated',
                actor: $user,
                target: $user,
                context: [
                    'channels' => $channelsPayload,
                    'categories' => $categoriesPayload,
                ],
            );
        });
    }
}
