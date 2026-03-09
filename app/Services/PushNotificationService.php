<?php

namespace App\Services;

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

        return PushSubscription::query()->updateOrCreate(
            ['endpoint_hash' => $hash],
            [
                'user_id' => $user->id,
                'endpoint' => $endpoint,
                'public_key' => (string) $payload['public_key'],
                'auth_token' => (string) $payload['auth_token'],
                'content_encoding' => $payload['content_encoding'] ?? 'aes128gcm',
                'is_active' => true,
                'meta' => $payload['meta'] ?? null,
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
        $subscriptions = PushSubscription::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        foreach ($subscriptions as $subscription) {
            Log::info('push.subscription.stub.sent', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        }

        return $subscriptions->count();
    }
}
