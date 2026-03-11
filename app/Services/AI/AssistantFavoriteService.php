<?php

namespace App\Services\AI;

use App\Models\AssistantFavorite;
use App\Models\User;
use App\Services\AI\Exceptions\AssistantFavoritesUnavailableException;
use Illuminate\Support\Facades\Schema;

class AssistantFavoriteService
{
    /**
     * @param array<string, mixed> $payload
     */
    public function store(User $user, array $payload): AssistantFavorite
    {
        $this->ensureFavoritesTableIsAvailable();

        $attributes = [
            'question' => trim((string) ($payload['question'] ?? '')),
            'answer' => trim((string) ($payload['answer'] ?? '')),
            'details' => array_values($payload['details'] ?? []),
            'sources' => array_values($payload['sources'] ?? []),
            'next_steps' => array_values($payload['next_steps'] ?? []),
        ];

        return AssistantFavorite::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'fingerprint' => $this->fingerprint($attributes),
            ],
            $attributes,
        );
    }

    public function delete(User $user, int $favoriteId): void
    {
        $this->ensureFavoritesTableIsAvailable();

        AssistantFavorite::query()
            ->where('user_id', $user->id)
            ->findOrFail($favoriteId)
            ->delete();
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function fingerprint(array $payload): string
    {
        return hash('sha256', json_encode([
            'question' => $payload['question'] ?? '',
            'answer' => $payload['answer'] ?? '',
            'details' => $payload['details'] ?? [],
            'sources' => $payload['sources'] ?? [],
            'next_steps' => $payload['next_steps'] ?? [],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    private function ensureFavoritesTableIsAvailable(): void
    {
        if (! Schema::hasTable('assistant_favorites')) {
            throw new AssistantFavoritesUnavailableException('Les favoris assistant ne sont pas encore initialises. Lancez les migrations puis rechargez la page.');
        }
    }
}
