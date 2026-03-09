<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * @param array<string, mixed> $payload
     */
    public function subscribe(User $user, array $payload): PushSubscription
    {
        $endpoint = (string) $payload['endpoint'];
        $hash = hash('sha256', $endpoint);
        $meta = is_array($payload['meta'] ?? null) ? $payload['meta'] : [];

        if (isset($payload['categories']) && is_array($payload['categories'])) {
            $meta['categories'] = array_values(array_filter(
                array_map(static fn ($value): string => (string) $value, $payload['categories']),
                static fn (string $value): bool => $value !== ''
            ));
        }

        return PushSubscription::query()->updateOrCreate(
            ['endpoint_hash' => $hash],
            [
                'user_id' => $user->id,
                'endpoint' => $endpoint,
                'public_key' => (string) $payload['public_key'],
                'auth_token' => (string) $payload['auth_token'],
                'content_encoding' => $payload['content_encoding'] ?? 'aes128gcm',
                'is_active' => true,
                'meta' => $meta ?: null,
                'last_seen_at' => now(),
            ],
        );
    }

    public function unsubscribe(User $user, string $endpoint): void
    {
        PushSubscription::query()
            ->where('user_id', $user->id)
            ->where('endpoint_hash', hash('sha256', $endpoint))
            ->update([
                'is_active' => false,
                'last_seen_at' => now(),
            ]);
    }

    public function sendToUser(User $user, string $title, string $message, array $data = []): int
    {
        $notification = new Notification([
            'user_id' => $user->id,
            'category' => (string) ($data['category'] ?? 'system'),
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);

        return $this->sendNotification($notification);
    }

    public function sendNotification(Notification $notification): int
    {
        $subscriptions = PushSubscription::query()
            ->where('user_id', $notification->user_id)
            ->where('is_active', true)
            ->get();

        $sent = 0;
        foreach ($subscriptions as $subscription) {
            if (! $this->acceptsCategory($subscription, (string) $notification->category)) {
                continue;
            }

            Log::info('push.subscription.stub.sent', [
                'subscription_id' => $subscription->id,
                'user_id' => $notification->user_id,
                'title' => $notification->title,
                'message' => $notification->message,
                'category' => $notification->category,
                'data' => $notification->data,
            ]);
            $sent++;
        }

        return $sent;
    }

    private function acceptsCategory(PushSubscription $subscription, string $category): bool
    {
        $meta = is_array($subscription->meta) ? $subscription->meta : [];
        $categories = $meta['categories'] ?? null;

        if (! is_array($categories) || $categories === []) {
            return true;
        }

        return in_array($category, $categories, true);
    }
}
