<?php

namespace App\Services\Admin;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Gift;
use App\Models\ShopItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminQuickActionService
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function setGiftStatus(User $actor, Gift $gift, bool $isActive): Gift
    {
        return DB::transaction(function () use ($actor, $gift, $isActive): Gift {
            $previous = (bool) $gift->is_active;
            $gift->is_active = $isActive;
            $gift->save();

            $this->storeAuditLogAction->execute(
                action: $isActive ? 'gifts.activated' : 'gifts.deactivated',
                actor: $actor,
                target: $gift,
                context: [
                    'gift_id' => $gift->id,
                    'previous_is_active' => $previous,
                    'is_active' => $isActive,
                ],
            );

            return $gift;
        });
    }

    public function updateGiftStock(User $actor, Gift $gift, int $stock): Gift
    {
        return DB::transaction(function () use ($actor, $gift, $stock): Gift {
            $previous = (int) $gift->stock;
            $gift->stock = max(0, $stock);
            $gift->save();

            $this->storeAuditLogAction->execute(
                action: 'gifts.stock.updated',
                actor: $actor,
                target: $gift,
                context: [
                    'gift_id' => $gift->id,
                    'previous_stock' => $previous,
                    'stock' => (int) $gift->stock,
                ],
            );

            return $gift;
        });
    }

    public function setShopItemStatus(User $actor, ShopItem $item, bool $isActive): ShopItem
    {
        return DB::transaction(function () use ($actor, $item, $isActive): ShopItem {
            $previous = (bool) $item->is_active;
            $item->is_active = $isActive;
            $item->save();

            $this->storeAuditLogAction->execute(
                action: $isActive ? 'shop.items.activated' : 'shop.items.deactivated',
                actor: $actor,
                target: $item,
                context: [
                    'shop_item_id' => $item->id,
                    'shop_item_key' => $item->key,
                    'previous_is_active' => $previous,
                    'is_active' => $isActive,
                ],
            );

            return $item;
        });
    }

    public function updateShopItemStock(User $actor, ShopItem $item, int $stock): ShopItem
    {
        return DB::transaction(function () use ($actor, $item, $stock): ShopItem {
            $previous = $item->stock !== null ? (int) $item->stock : null;
            $item->stock = max(0, $stock);
            $item->save();

            $this->storeAuditLogAction->execute(
                action: 'shop.items.stock.updated',
                actor: $actor,
                target: $item,
                context: [
                    'shop_item_id' => $item->id,
                    'shop_item_key' => $item->key,
                    'previous_stock' => $previous,
                    'stock' => (int) $item->stock,
                ],
            );

            return $item;
        });
    }
}

