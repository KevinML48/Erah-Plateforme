<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Rewards\RedeemGiftAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Gifts\AddGiftToCartRequest;
use App\Http\Requests\Web\Gifts\CheckoutGiftCartRequest;
use App\Http\Requests\Web\Gifts\UpdateGiftCartItemRequest;
use App\Http\Requests\Web\RedeemGiftRequest;
use App\Models\Gift;
use App\Models\GiftCartItem;
use App\Models\GiftRedemption;
use App\Models\GiftRedemptionEvent;
use App\Models\User;
use App\Models\UserRewardWallet;
use App\Support\LaunchGiftCatalog;
use App\Services\Gifts\GiftCartService;
use App\Services\Gifts\GiftFavoriteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class GiftPageController extends Controller
{
    public function index(
        Request $request,
        GiftFavoriteService $giftFavoriteService,
        GiftCartService $giftCartService
    ): View {
        $user = auth()->user();
        $isAuthenticated = $user instanceof User;

        $wallet = null;
        $walletBalance = 0;
        $recentRedemptions = collect();
        $favoriteGiftIds = [];
        $favoriteGifts = collect();
        $cartItemsCount = 0;

        if ($isAuthenticated) {
            $wallet = UserRewardWallet::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            );
            $walletBalance = (int) ($wallet->balance ?? 0);
            $favoriteGiftIds = $giftFavoriteService->favoriteGiftIds($user);
            $favoriteGifts = $giftFavoriteService->list($user)
                ->filter(fn ($favorite) => $favorite->gift !== null)
                ->take(6)
                ->values();
            $cartItemsCount = $giftCartService->countItems($user);

            $recentRedemptions = GiftRedemption::query()
                ->where('user_id', $user->id)
                ->with('gift')
                ->latest('requested_at')
                ->limit(8)
                ->get();
        }

        $selectedSort = (string) $request->query('sort', 'featured');
        $searchTerm = trim((string) $request->query('search', ''));
        $selectedAvailability = (string) $request->query('availability', 'all');

        $sortOptions = [
            'featured' => 'Mise en avant',
            'cost_asc' => 'Cout points: croissant',
            'cost_desc' => 'Cout points: decroissant',
            'recent' => 'Nouveautes',
        ];

        $availabilityOptions = [
            'all' => 'Toutes disponibilites',
            'available' => 'Demandables',
            'low' => 'Stock limite',
            'out' => 'Rupture / indisponible',
        ];

        if (! array_key_exists($selectedSort, $sortOptions)) {
            $selectedSort = 'featured';
        }

        if (! array_key_exists($selectedAvailability, $availabilityOptions)) {
            $selectedAvailability = 'all';
        }

        $catalogQuery = Gift::query()->where('is_active', true);
        match ($selectedSort) {
            'cost_asc' => $catalogQuery->orderBy('cost_points'),
            'cost_desc' => $catalogQuery->orderByDesc('cost_points'),
            'recent' => $catalogQuery->orderByDesc('created_at'),
            default => $catalogQuery
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('cost_points'),
        };

        $catalog = $catalogQuery->get();

        $giftCards = $catalog
            ->map(function (Gift $gift) use ($walletBalance, $favoriteGiftIds): array {
                $categoryKey = $this->resolveCategoryKey($gift);
                $availabilityKey = $this->resolveAvailabilityKey($gift);
                $costPoints = (int) $gift->cost_points;
                $pointsMissing = max(0, $costPoints - $walletBalance);

                return [
                    'gift' => $gift,
                    'category_key' => $categoryKey,
                    'category_label' => $this->categoryLabel($categoryKey),
                    'delivery_type' => $gift->launchCatalogDeliveryType(),
                    'availability_key' => $availabilityKey,
                    'availability_label' => $this->availabilityLabel($availabilityKey),
                    'availability_copy' => $this->availabilityCopy($availabilityKey),
                    'can_redeem' => $availabilityKey !== 'unavailable' && $availabilityKey !== 'out',
                    'points_missing' => $pointsMissing,
                    'is_favorited' => in_array((int) $gift->id, $favoriteGiftIds, true),
                    'lead_time' => $this->leadTimeCopy($gift, $availabilityKey),
                ];
            })
            ->values();

        $categories = $giftCards
            ->pluck('category_key')
            ->unique()
            ->values()
            ->map(fn (string $key): array => [
                'key' => $key,
                'label' => $this->categoryLabel($key),
            ]);

        $selectedCategory = (string) $request->query('category', 'all');

        if ($selectedCategory !== 'all' && ! $categories->pluck('key')->contains($selectedCategory)) {
            $selectedCategory = 'all';
        }

        $filteredCards = $giftCards
            ->when($selectedCategory !== 'all', fn (Collection $cards) => $cards->where('category_key', $selectedCategory))
            ->when($selectedAvailability !== 'all', fn (Collection $cards) => match ($selectedAvailability) {
                'available' => $cards->whereIn('availability_key', ['available', 'low']),
                'low' => $cards->where('availability_key', 'low'),
                'out' => $cards->whereIn('availability_key', ['out', 'unavailable']),
                default => $cards,
            })
            ->when($searchTerm !== '', function (Collection $cards) use ($searchTerm): Collection {
                $needle = Str::lower($searchTerm);

                return $cards->filter(function (array $card) use ($needle): bool {
                    /** @var Gift $gift */
                    $gift = $card['gift'];
                    $haystack = Str::lower(trim((string) $gift->title.' '.(string) $gift->description));

                    return Str::contains($haystack, $needle);
                });
            })
            ->values();

        return view('pages.gifts.index', [
            'wallet' => $wallet,
            'walletBalance' => $walletBalance,
            'giftCards' => $filteredCards,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'selectedAvailability' => $selectedAvailability,
            'availabilityOptions' => $availabilityOptions,
            'selectedSort' => $selectedSort,
            'sortOptions' => $sortOptions,
            'searchTerm' => $searchTerm,
            'recentRedemptions' => $recentRedemptions,
            'favoriteGifts' => $favoriteGifts,
            'cartItemsCount' => $cartItemsCount,
            'isAuthenticated' => $isAuthenticated,
            'giftIndexRouteName' => $isAuthenticated ? 'gifts.index' : 'app.gifts.index',
            'giftShowRouteName' => $isAuthenticated ? 'gifts.show' : 'app.gifts.show',
            'statusLabels' => GiftRedemption::statusLabels(),
        ]);
    }

    public function show(
        int $giftId,
        GiftFavoriteService $giftFavoriteService
    ): View {
        $user = auth()->user();
        $isAuthenticated = $user instanceof User;

        $wallet = null;
        $walletBalance = 0;
        $myRecentRedemptions = collect();
        $favoriteGiftIds = [];
        $cartItemQuantity = 0;

        if ($isAuthenticated) {
            $wallet = UserRewardWallet::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            );
            $walletBalance = (int) ($wallet->balance ?? 0);
            $favoriteGiftIds = $giftFavoriteService->favoriteGiftIds($user);
        }

        $gift = Gift::query()->findOrFail($giftId);
        $categoryKey = $this->resolveCategoryKey($gift);
        $giftCost = (int) $gift->cost_points;
        $giftStock = (int) $gift->stock;
        $isRedeemable = $gift->is_active && $giftStock > 0;
        $pointsMissing = max(0, $giftCost - $walletBalance);

        if ($isAuthenticated) {
            $myRecentRedemptions = GiftRedemption::query()
                ->where('user_id', $user->id)
                ->where('gift_id', $gift->id)
                ->latest('requested_at')
                ->limit(10)
                ->get();

            $cartItemQuantity = (int) GiftCartItem::query()
                ->where('user_id', $user->id)
                ->where('gift_id', $gift->id)
                ->value('quantity');
        }

        return view('pages.gifts.show', [
            'wallet' => $wallet,
            'gift' => $gift,
            'giftCategoryKey' => $categoryKey,
            'giftCategoryLabel' => $this->categoryLabel($categoryKey),
            'giftCover' => $gift->image_url ?: '/template/assets/img/logo.png',
            'walletBalance' => $walletBalance,
            'giftCost' => $giftCost,
            'giftStock' => $giftStock,
            'isRedeemable' => $isRedeemable,
            'canAffordGift' => $isAuthenticated && $walletBalance >= $giftCost,
            'pointsMissing' => $pointsMissing,
            'myRecentRedemptions' => $myRecentRedemptions,
            'isAuthenticated' => $isAuthenticated,
            'isFavorited' => in_array((int) $gift->id, $favoriteGiftIds, true),
            'cartItemQuantity' => $cartItemQuantity,
            'giftIndexRouteName' => $isAuthenticated ? 'gifts.index' : 'app.gifts.index',
            'statusLabels' => GiftRedemption::statusLabels(),
        ]);
    }

    public function redeem(
        RedeemGiftRequest $request,
        int $giftId,
        RedeemGiftAction $redeemGiftAction
    ): RedirectResponse {
        try {
            $result = $redeemGiftAction->execute(
                user: $request->user(),
                giftId: $giftId,
                idempotencyKey: (string) $request->validated('idempotency_key')
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $this->friendlyRedeemError($exception->getMessage()));
        }

        $orderNumber = $this->formatOrderNumber((int) $result['redemption']->id);

        if ($result['idempotent']) {
            return back()->with('success', 'Commande '.$orderNumber.' deja enregistree. Retrouvez-la dans "Mes commandes cadeaux".');
        }

        return back()->with('success', 'Demande enregistree: '.$orderNumber.'. Vous pouvez suivre chaque etape depuis vos commandes cadeaux.');
    }

    public function cart(Request $request, GiftCartService $giftCartService): View
    {
        $summary = $giftCartService->summarize($request->user());

        return view('pages.gifts.cart', [
            'summary' => $summary,
        ]);
    }

    public function addToCart(
        AddGiftToCartRequest $request,
        int $giftId,
        GiftCartService $giftCartService
    ): RedirectResponse {
        $quantity = (int) ($request->validated()['quantity'] ?? 1);

        try {
            $giftCartService->add($request->user(), $giftId, $quantity);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $this->friendlyCartError($exception->getMessage()));
        }

        return back()->with('success', 'Cadeau ajoute au panier.');
    }

    public function updateCartItem(
        UpdateGiftCartItemRequest $request,
        int $itemId,
        GiftCartService $giftCartService
    ): RedirectResponse {
        $quantity = (int) $request->validated('quantity');

        try {
            $giftCartService->updateQuantity($request->user(), $itemId, $quantity);
        } catch (RuntimeException $exception) {
            return back()->with('error', $this->friendlyCartError($exception->getMessage()));
        }

        return back()->with('success', 'Quantite mise a jour dans le panier.');
    }

    public function removeCartItem(
        Request $request,
        int $itemId,
        GiftCartService $giftCartService
    ): RedirectResponse {
        try {
            $giftCartService->remove($request->user(), $itemId);
        } catch (RuntimeException $exception) {
            return back()->with('error', $this->friendlyCartError($exception->getMessage()));
        }

        return back()->with('success', 'Cadeau retire du panier.');
    }

    public function checkoutCart(
        CheckoutGiftCartRequest $request,
        GiftCartService $giftCartService
    ): RedirectResponse {
        try {
            $result = $giftCartService->checkout(
                user: $request->user(),
                idempotencyKey: (string) $request->validated('idempotency_key'),
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $this->friendlyCartError($exception->getMessage()));
        }

        if ($result['idempotent']) {
            return redirect()
                ->route('gifts.redemptions')
                ->with('success', 'Validation deja prise en compte. Consultez vos commandes cadeaux.');
        }

        return redirect()
            ->route('gifts.redemptions')
            ->with('success', 'Panier valide: '.$result['redemptions']->count().' commande(s) créée(s), '.$result['total_points'].' points débites.');
    }

    public function favorites(
        Request $request,
        GiftFavoriteService $giftFavoriteService,
        GiftCartService $giftCartService
    ): View
    {
        $user = $request->user();
        $favorites = $giftFavoriteService->list($user);
        $summary = $giftCartService->summarize($user);

        return view('pages.gifts.favorites', [
            'favorites' => $favorites,
            'cartSummary' => $summary,
        ]);
    }

    public function toggleFavorite(
        Request $request,
        int $giftId,
        GiftFavoriteService $giftFavoriteService
    ): RedirectResponse {
        $result = $giftFavoriteService->toggle($request->user(), $giftId);

        return back()->with(
            'success',
            $result['is_favorited']
                ? 'Cadeau ajoute a vos favoris.'
                : 'Cadeau retire de vos favoris.'
        );
    }

    public function redemptions(Request $request): View
    {
        $statuses = GiftRedemption::statuses();
        $selectedStatus = (string) $request->query('status', 'all');
        $searchTerm = trim((string) $request->query('search', ''));

        if ($selectedStatus !== 'all' && ! in_array($selectedStatus, $statuses, true)) {
            $selectedStatus = 'all';
        }

        $redemptionsQuery = GiftRedemption::query()
            ->where('user_id', auth()->id())
            ->with('gift');

        if ($selectedStatus !== 'all') {
            $redemptionsQuery->where('status', $selectedStatus);
        }

        if ($searchTerm !== '') {
            $redemptionsQuery->where(function ($query) use ($searchTerm): void {
                $query->where('tracking_code', 'like', '%'.$searchTerm.'%')
                    ->orWhereHas('gift', fn ($giftQuery) => $giftQuery->where('title', 'like', '%'.$searchTerm.'%'));

                if (is_numeric($searchTerm)) {
                    $query->orWhereKey((int) $searchTerm);
                }
            });
        }

        $redemptions = $redemptionsQuery
            ->latest('requested_at')
            ->paginate(20)
            ->withQueryString();

        return view('pages.gifts.redemptions', [
            'redemptions' => $redemptions,
            'selectedStatus' => $selectedStatus,
            'statuses' => $statuses,
            'statusLabels' => GiftRedemption::statusLabels(),
            'searchTerm' => $searchTerm,
        ]);
    }

    public function redemption(int $redemptionId): View
    {
        $redemption = GiftRedemption::query()
            ->where('user_id', auth()->id())
            ->whereKey($redemptionId)
            ->with([
                'gift',
                'events' => fn ($query) => $query
                    ->with('actor:id,name')
                    ->orderByDesc('created_at'),
            ])
            ->firstOrFail();

        $events = $redemption->events
            ->map(fn (GiftRedemptionEvent $event): array => $this->mapRedemptionEvent($event, $redemption))
            ->values();

        return view('pages.gifts.redemption-show', [
            'redemption' => $redemption,
            'orderNumber' => $this->formatOrderNumber((int) $redemption->id),
            'statusLabel' => GiftRedemption::statusLabel((string) $redemption->status),
            'statusLabels' => GiftRedemption::statusLabels(),
            'timelineSteps' => $this->buildRedemptionTimeline($redemption),
            'timelineEvents' => $events,
            'trackingUrl' => $this->buildTrackingUrl($redemption->tracking_code),
        ]);
    }

    public function wallet(): RedirectResponse
    {
        return redirect()->route('wallet.index');
    }

    private function resolveCategoryKey(Gift $gift): string
    {
        $catalogDefinition = LaunchGiftCatalog::definitionForGift($gift);
        if (is_array($catalogDefinition) && is_string($catalogDefinition['category'] ?? null)) {
            return (string) $catalogDefinition['category'];
        }

        $content = Str::lower(trim(($gift->title ?? '').' '.($gift->description ?? '')));

        if (
            Str::contains($content, ['badge', 'contour', 'avatar', 'banniere', 'titre', 'profil', 'pseudo', 'theme', 'pack profil'])
        ) {
            return 'profile_digital';
        }

        if (
            Str::contains($content, ['gain libre', 'amazon', 'cash', 'virement', 'bon d achat'])
        ) {
            return 'manual_reward';
        }

        if (
            Str::contains($content, ['nitro', 'steam', 'riot', 'valorant', 'playstation', 'xbox', 'nintendo', 'carte cadeau', 'amazon', 'gain libre'])
        ) {
            return 'digital_reward';
        }

        if (
            Str::contains($content, ['souris gaming', 'casque gaming', 'clavier gaming', 'ecran gaming', 'chaise gaming'])
        ) {
            return 'premium';
        }

        if (
            Str::contains($content, ['support telephone', 'pochette', 'lampe', 'support casque', 'tapis de souris', 'maillot'])
        ) {
            return 'physical';
        }

        if ((int) $gift->cost_points >= 12000) {
            return 'premium';
        }

        return 'digital_reward';
    }

    private function categoryLabel(string $key): string
    {
        return match ($key) {
            'profile_digital' => 'Profil numerique',
            'digital_reward' => 'Digital',
            'manual_reward' => 'Recompense manuelle',
            'physical' => 'Physique',
            'premium' => 'Premium',
            default => 'Catalogue',
        };
    }

    private function leadTimeCopy(Gift $gift, string $availabilityKey): string
    {
        if ($availabilityKey !== 'available' && $availabilityKey !== 'low') {
            return 'Retour en stock des que possible';
        }

        return match ($gift->launchCatalogDeliveryType()) {
            'profile' => 'Livraison immediate sur le profil',
            'digital' => 'Traitement admin digital sous 48h',
            'manual' => 'Validation et traitement manuel',
            'physical' => 'Traitement expedition sous 48h',
            'premium' => 'Validation premium manuelle',
            default => 'Traitement admin en moyenne sous 48h',
        };
    }

    private function resolveAvailabilityKey(Gift $gift): string
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

    private function availabilityLabel(string $key): string
    {
        return match ($key) {
            'available' => 'Disponible',
            'low' => 'Stock limite',
            'out' => 'Bientot de retour',
            default => 'Indisponible',
        };
    }

    private function availabilityCopy(string $key): string
    {
        return match ($key) {
            'available' => 'Demande immediate possible.',
            'low' => 'Peu d exemplaires restants.',
            'out' => 'Rupture temporaire.',
            default => 'Cadeau temporairement indisponible.',
        };
    }

    private function friendlyRedeemError(string $message): string
    {
        $normalizedMessage = Str::lower($message);

        if (Str::contains($normalizedMessage, ['insufficient', 'solde', 'balance', 'point'])) {
            return 'Solde insuffisant: il vous manque des points pour valider cette demande.';
        }

        if (Str::contains($normalizedMessage, ['stock', 'rupture'])) {
            return 'Ce cadeau est en rupture pour le moment. Revenez plus tard.';
        }

        if (Str::contains($normalizedMessage, ['possede deja', 'profil'])) {
            return 'Cet objet de profil est deja dans votre collection.';
        }

        if (Str::contains($normalizedMessage, ['disponible', 'inactive', 'désactivée'])) {
            return 'Ce cadeau est temporairement indisponible.';
        }

        return 'Impossible de traiter votre demande cadeau pour le moment. Merci de reessayer.';
    }

    private function friendlyCartError(string $message): string
    {
        $normalizedMessage = Str::lower($message);

        if (Str::contains($normalizedMessage, ['insufficient', 'solde', 'balance', 'point'])) {
            return 'Solde insuffisant: votre panier depasse votre reserve de points.';
        }

        if (Str::contains($normalizedMessage, ['stock', 'rupture'])) {
            return 'Stock insuffisant sur un des cadeaux du panier. Ajustez les quantites puis reessayez.';
        }

        if (Str::contains($normalizedMessage, ['possede deja', 'profil'])) {
            return 'Un objet de profil du panier est deja dans votre collection.';
        }

        if (Str::contains($normalizedMessage, ['désactivée', 'indisponible'])) {
            return 'Un des cadeaux du panier n'est plus disponible.';
        }

        return $message;
    }

    /**
     * @return array<int, array{key: string, label: string, at: string|null, state: string}>
     */
    private function buildRedemptionTimeline(GiftRedemption $redemption): array
    {
        $steps = [
            [
                'key' => GiftRedemption::STATUS_PENDING,
                'label' => 'Demande envoyee',
                'at' => $redemption->requested_at?->format('d/m/Y H:i'),
            ],
            [
                'key' => GiftRedemption::STATUS_APPROVED,
                'label' => 'Demande approuvee',
                'at' => $redemption->approved_at?->format('d/m/Y H:i'),
            ],
            [
                'key' => GiftRedemption::STATUS_SHIPPED,
                'label' => 'Commande expediee',
                'at' => $redemption->shipped_at?->format('d/m/Y H:i'),
            ],
            [
                'key' => GiftRedemption::STATUS_DELIVERED,
                'label' => 'Commande livree',
                'at' => $redemption->delivered_at?->format('d/m/Y H:i'),
            ],
            [
                'key' => GiftRedemption::STATUS_REJECTED,
                'label' => 'Demande rejetee',
                'at' => $redemption->rejected_at?->format('d/m/Y H:i'),
            ],
        ];

        if ((string) $redemption->status === GiftRedemption::STATUS_CANCELLED) {
            $steps[] = [
                'key' => GiftRedemption::STATUS_CANCELLED,
                'label' => 'Commande annulee',
                'at' => $this->resolveEventTimestamp($redemption, ['admin_cancelled']),
            ];
        }

        if ((string) $redemption->status === GiftRedemption::STATUS_REFUNDED) {
            $steps[] = [
                'key' => GiftRedemption::STATUS_REFUNDED,
                'label' => 'Points rembourses',
                'at' => $this->resolveEventTimestamp($redemption, ['admin_refunded']),
            ];
        }

        return collect($steps)
            ->map(function (array $step) use ($redemption): array {
                $step['state'] = $this->resolveTimelineStepState($step['key'], $redemption);

                return $step;
            })
            ->values()
            ->all();
    }

    private function resolveTimelineStepState(string $stepKey, GiftRedemption $redemption): string
    {
        $currentStatus = (string) $redemption->status;

        if ($currentStatus === GiftRedemption::STATUS_REJECTED) {
            return match ($stepKey) {
                GiftRedemption::STATUS_PENDING => 'complèted',
                GiftRedemption::STATUS_REJECTED => 'current',
                default => 'skipped',
            };
        }

        if ($currentStatus === GiftRedemption::STATUS_CANCELLED) {
            if ($stepKey === GiftRedemption::STATUS_CANCELLED) {
                return 'current';
            }

            if ($stepKey === GiftRedemption::STATUS_PENDING) {
                return 'complèted';
            }

            if ($stepKey === GiftRedemption::STATUS_APPROVED) {
                return $redemption->approved_at ? 'complèted' : 'skipped';
            }

            if ($stepKey === GiftRedemption::STATUS_SHIPPED) {
                return $redemption->shipped_at ? 'complèted' : 'skipped';
            }

            return 'skipped';
        }

        if ($currentStatus === GiftRedemption::STATUS_REFUNDED) {
            return $stepKey === GiftRedemption::STATUS_REFUNDED ? 'current' : 'complèted';
        }

        $normalOrder = [
            GiftRedemption::STATUS_PENDING => 0,
            GiftRedemption::STATUS_APPROVED => 1,
            GiftRedemption::STATUS_SHIPPED => 2,
            GiftRedemption::STATUS_DELIVERED => 3,
        ];

        if (! array_key_exists($stepKey, $normalOrder)) {
            return 'skipped';
        }

        $currentRank = $normalOrder[$currentStatus] ?? 0;
        $stepRank = $normalOrder[$stepKey];

        if ($stepRank < $currentRank) {
            return 'complèted';
        }

        if ($stepRank === $currentRank) {
            return 'current';
        }

        return 'upcoming';
    }

    private function resolveEventTimestamp(GiftRedemption $redemption, array $types): ?string
    {
        $event = $redemption->events
            ->first(fn (GiftRedemptionEvent $eventItem): bool => in_array($eventItem->type, $types, true));

        return $event?->created_at?->format('d/m/Y H:i');
    }

    /**
     * @return array{title: string, summary: string, actor: string, happened_at: string|null, type: string}
     */
    private function mapRedemptionEvent(GiftRedemptionEvent $event, GiftRedemption $redemption): array
    {
        $data = is_array($event->data) ? $event->data : [];

        $title = match ($event->type) {
            'redeem_requested' => 'Demande envoyee',
            'admin_approved' => 'Demande approuvee',
            'admin_rejected' => 'Demande rejetee',
            'admin_shipped' => 'Commande expediee',
            'admin_delivered' => 'Commande livree',
            'admin_cancelled' => 'Commande annulee',
            'admin_refunded' => 'Points rembourses',
            default => Str::headline(str_replace('_', ' ', (string) $event->type)),
        };

        $summary = match ($event->type) {
            'redeem_requested' => 'Votre demande a ete enregistree et transmise a l equipe admin.',
            'admin_approved' => 'La commande est validee et en preparation.',
            'admin_rejected' => 'La commande a ete rejetee.'
                .($data['reason'] ?? $redemption->reason ? ' Motif: '.($data['reason'] ?? $redemption->reason).'.' : ''),
            'admin_shipped' => trim(collect([
                'Le colis a ete expedie.',
                isset($data['tracking_carrier']) && (string) $data['tracking_carrier'] !== '' ? 'Transporteur: '.$data['tracking_carrier'].'.' : null,
                isset($data['tracking_code']) && (string) $data['tracking_code'] !== '' ? 'Tracking: '.$data['tracking_code'].'.' : null,
                isset($data['shipping_note']) && (string) $data['shipping_note'] !== '' ? 'Note: '.$data['shipping_note'].'.' : null,
            ])->filter()->implode(' ')),
            'admin_delivered' => 'La livraison est confirmee.',
            'admin_cancelled' => 'La commande a ete annulee par l equipe admin.',
            'admin_refunded' => 'Les points associes a cette commande ont ete rembourses.',
            default => 'Mise a jour enregistree sur votre commande cadeau.',
        };

        return [
            'title' => $title,
            'summary' => $summary,
            'actor' => $event->actor?->name ?? 'Systeme',
            'happened_at' => $event->created_at?->format('d/m/Y H:i'),
            'type' => (string) $event->type,
        ];
    }

    private function buildTrackingUrl(?string $trackingCode): ?string
    {
        $normalizedCode = trim((string) $trackingCode);

        if ($normalizedCode === '') {
            return null;
        }

        return 'https://www.17track.net/fr/track#nums='.urlencode($normalizedCode);
    }

    private function formatOrderNumber(int $id): string
    {
        return 'CMD-'.str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }
}
