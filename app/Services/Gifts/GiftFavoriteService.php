<?php

namespace App\Services\Gifts;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\ActivityEvent;
use App\Models\Gift;
use App\Models\GiftFavorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GiftFavoriteService
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return array{is_favorited: bool, favorite: GiftFavorite|null}
     */
    public function toggle(User $user, int $giftId): array
    {
        return DB::transaction(function () use ($user, $giftId): array {
            $gift = Gift::query()->findOrFail($giftId);

            $favorite = GiftFavorite::query()
                ->where('user_id', $user->id)
                ->where('gift_id', $gift->id)
                ->lockForUpdate()
                ->first();

            if ($favorite) {
                $favorite->delete();

                $this->storeActivityEvent(
                    user: $user,
                    eventType: ActivityEvent::TYPE_GIFT_FAVORITE_REMOVE,
                    gift: $gift,
                );

                $this->storeAuditLogAction->execute(
                    action: 'gift.favorite.remove',
                    actor: $user,
                    target: $gift,
                    context: ['gift_id' => $gift->id],
                );

                return [
                    'is_favorited' => false,
                    'favorite' => null,
                ];
            }

            $favorite = GiftFavorite::query()->create([
                'user_id' => $user->id,
                'gift_id' => $gift->id,
            ]);

            $this->storeActivityEvent(
                user: $user,
                eventType: ActivityEvent::TYPE_GIFT_FAVORITE_ADD,
                gift: $gift,
            );

            $this->storeAuditLogAction->execute(
                action: 'gift.favorite.add',
                actor: $user,
                target: $gift,
                context: ['gift_id' => $gift->id],
            );

            return [
                'is_favorited' => true,
                'favorite' => $favorite,
            ];
        });
    }

    /**
     * @return EloquentCollection<int, GiftFavorite>
     */
    public function list(User $user): EloquentCollection
    {
        return GiftFavorite::query()
            ->where('user_id', $user->id)
            ->with('gift')
            ->latest()
            ->get();
    }

    /**
     * @return array<int, int>
     */
    public function favoriteGiftIds(User $user): array
    {
        return GiftFavorite::query()
            ->where('user_id', $user->id)
            ->pluck('gift_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function storeActivityEvent(User $user, string $eventType, Gift $gift, array $metadata = []): void
    {
        ActivityEvent::query()->create([
            'user_id' => $user->id,
            'event_type' => $eventType,
            'ref_type' => 'gift',
            'ref_id' => (string) $gift->id,
            'occurred_at' => now(),
            'unique_key' => $eventType.':'.Str::uuid()->toString(),
            'metadata' => array_merge([
                'gift_id' => $gift->id,
                'gift_title' => $gift->title,
            ], $metadata),
            'created_at' => now(),
        ]);
    }
}

