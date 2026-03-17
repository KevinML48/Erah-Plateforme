<?php

namespace App\Services\Gifts;

use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\User;
use App\Models\UserRewardWallet;
use App\Services\ProfileCosmeticService;
use App\Services\SupporterAccessResolver;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

class GiftEligibilityService
{
    public function __construct(
        private readonly ProfileCosmeticService $profileCosmeticService,
        private readonly SupporterAccessResolver $supporterAccessResolver,
    ) {
    }

    public function resolvePublicGift(string|int $identifier): Gift
    {
        $query = Gift::query()->where('is_active', true);

        if (is_numeric($identifier)) {
            return $query->whereKey((int) $identifier)->firstOrFail();
        }

        return $query
            ->where(function (Builder $giftQuery) use ($identifier): void {
                $giftQuery->where('slug', trim((string) $identifier));

                $key = trim((string) $identifier);
                if ($key !== '') {
                    $giftQuery->orWhere('key', $key);
                }
            })
            ->firstOrFail();
    }

    public function assertPurchasable(User $user, Gift $gift, int $quantity = 1): void
    {
        if (! $gift->is_active) {
            throw new RuntimeException('Ce cadeau n est pas disponible.');
        }

        if ((int) $gift->stock < max(1, $quantity)) {
            throw new RuntimeException('Stock indisponible pour ce cadeau.');
        }

        if ($gift->supporterOnly() && ! $this->supporterAccessResolver->hasActiveSupport($user)) {
            throw new RuntimeException('Ce cadeau est reserve aux supporters actifs.');
        }

        $blockedReason = $this->profileCosmeticService->blocksRepurchase($user, $gift);
        if ($blockedReason !== null) {
            throw new RuntimeException($blockedReason);
        }

        if (! $gift->isRepeatable() && $this->hasExistingActiveRedemption($user, $gift)) {
            throw new RuntimeException('Ce cadeau ne peut etre commande qu une seule fois.');
        }
    }

    public function availabilityKey(Gift $gift): string
    {
        if (! $gift->is_active) {
            return 'unavailable';
        }

        if ((int) $gift->stock <= 0) {
            return 'out';
        }

        if ((int) $gift->stock <= 5) {
            return 'low';
        }

        return 'available';
    }

    /**
     * @return array{
     *     state: string,
     *     can_redeem: bool,
     *     can_add_to_cart: bool,
     *     wallet_balance: int,
     *     points_missing: int,
     *     title: string,
     *     message: string
     * }
     */
    public function evaluate(?User $user, Gift $gift, int $quantity = 1): array
    {
        $quantity = max(1, $quantity);
        $walletBalance = 0;
        $pointsMissing = 0;

        if ($user) {
            $walletBalance = (int) UserRewardWallet::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            )->balance;
            $pointsMissing = max(0, ((int) $gift->cost_points * $quantity) - $walletBalance);
        }

        if (! $gift->is_active) {
            return $this->state('unavailable', false, false, $walletBalance, $pointsMissing, 'Cadeau indisponible', 'Cette fiche n est plus ouverte a la commande.');
        }

        if ((int) $gift->stock < $quantity) {
            return $this->state('out', false, false, $walletBalance, $pointsMissing, 'Rupture de stock', 'Le stock disponible ne permet plus de lancer cette demande.');
        }

        if (! $user) {
            return $this->state('auth_required', false, false, $walletBalance, $pointsMissing, 'Connexion requise', 'Connectez-vous pour verifier votre solde, ajouter ce cadeau au panier et lancer son achat.');
        }

        if ($gift->supporterOnly() && ! $this->supporterAccessResolver->hasActiveSupport($user)) {
            return $this->state('supporter_required', false, false, $walletBalance, $pointsMissing, 'Reserve aux supporters', 'Ce cadeau n est accessible qu aux supporters actifs.');
        }

        $blockedReason = $this->profileCosmeticService->blocksRepurchase($user, $gift);
        if ($blockedReason !== null) {
            return $this->state('already_owned', false, false, $walletBalance, $pointsMissing, 'Deja possede', 'Cet objet est deja dans votre collection et ne peut pas etre rachete.');
        }

        if (! $gift->isRepeatable() && $this->hasExistingActiveRedemption($user, $gift)) {
            return $this->state('already_ordered', false, false, $walletBalance, $pointsMissing, 'Commande deja en cours', 'Ce cadeau ne peut etre commande qu une seule fois par compte.');
        }

        if ($pointsMissing > 0) {
            return $this->state('insufficient_points', false, true, $walletBalance, $pointsMissing, 'Points insuffisants', 'Votre solde actuel ne permet pas de finaliser cette commande.');
        }

        return $this->state('available', true, true, $walletBalance, 0, 'Disponible maintenant', 'Tous les criteres sont remplis pour lancer l achat ou l echange.');
    }

    /**
     * @return array{state: string, can_redeem: bool, can_add_to_cart: bool, wallet_balance: int, points_missing: int, title: string, message: string}
     */
    private function state(
        string $state,
        bool $canRedeem,
        bool $canAddToCart,
        int $walletBalance,
        int $pointsMissing,
        string $title,
        string $message,
    ): array {
        return [
            'state' => $state,
            'can_redeem' => $canRedeem,
            'can_add_to_cart' => $canAddToCart,
            'wallet_balance' => $walletBalance,
            'points_missing' => $pointsMissing,
            'title' => $title,
            'message' => $message,
        ];
    }

    private function hasExistingActiveRedemption(User $user, Gift $gift): bool
    {
        return GiftRedemption::query()
            ->where('user_id', $user->id)
            ->where('gift_id', $gift->id)
            ->whereIn('status', [
                GiftRedemption::STATUS_PENDING,
                GiftRedemption::STATUS_APPROVED,
                GiftRedemption::STATUS_SHIPPED,
                GiftRedemption::STATUS_DELIVERED,
            ])
            ->exists();
    }
}