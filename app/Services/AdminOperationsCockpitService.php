<?php

namespace App\Services;

use App\Models\ActivityEvent;
use App\Models\AuditLog;
use App\Models\Bet;
use App\Models\Clip;
use App\Models\ClubReview;
use App\Models\Duel;
use App\Models\EsportMatch;
use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\LiveCode;
use App\Models\MissionTemplate;
use App\Models\RewardWalletTransaction;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserPurchase;
use App\Models\UserRewardWallet;
use App\Models\UserSupportSubscription;
use App\Models\UserWallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminOperationsCockpitService
{
    private const LOW_STOCK_THRESHOLD = 5;
    private const LONG_PENDING_HOURS = 48;

    /** @var array<string, bool> */
    private array $tablePresence = [];

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function dashboardPayload(array $filters = []): array
    {
        $normalizedFilters = $this->normalizeFilters($filters);
        $kpis = $this->buildKpis();
        $pending = $this->buildPendingOperations(limit: 8);
        $alerts = $this->buildAlerts();

        $feed = $this->buildFeed(
            source: $normalizedFilters['source'],
            module: $normalizedFilters['module'],
            type: $normalizedFilters['type'],
            search: $normalizedFilters['feed_q'],
            perPage: $normalizedFilters['per_page'],
            page: $normalizedFilters['page'],
        );

        return [
            'kpis' => $kpis,
            'pending' => $pending,
            'alerts' => $alerts,
            'feed' => $feed,
            'feed_filters' => $normalizedFilters,
            'feed_options' => [
                'sources' => $this->feedSourceOptions(),
                'modules' => $this->feedModuleOptions(),
                'types' => $this->feedTypeOptions(),
            ],
            'search' => $this->buildGlobalSearch($normalizedFilters['global_q']),
            'quick_links' => $this->quickLinks($kpis, $pending),
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function livePayload(array $filters = []): array
    {
        $normalizedFilters = $this->normalizeFilters($filters);
        $kpis = $this->buildKpis();
        $pending = $this->buildPendingOperations(limit: 5);
        $alerts = $this->buildAlerts();

        $feed = $this->buildFeed(
            source: $normalizedFilters['source'],
            module: $normalizedFilters['module'],
            type: $normalizedFilters['type'],
            search: $normalizedFilters['feed_q'],
            perPage: 12,
            page: 1,
        );

        return [
            'kpis' => $kpis,
            'pending' => $pending,
            'alerts' => $alerts,
            'feed_items' => collect($feed->items()),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    private function normalizeFilters(array $filters): array
    {
        $source = strtolower(trim((string) ($filters['feed_source'] ?? 'all')));
        if (! array_key_exists($source, $this->feedSourceOptions())) {
            $source = 'all';
        }

        $module = strtolower(trim((string) ($filters['feed_module'] ?? 'all')));
        if (! array_key_exists($module, $this->feedModuleOptions())) {
            $module = 'all';
        }

        $type = strtolower(trim((string) ($filters['feed_type'] ?? 'all')));
        if (! array_key_exists($type, $this->feedTypeOptions())) {
            $type = 'all';
        }

        $feedQuery = trim((string) ($filters['feed_q'] ?? ''));
        $globalQuery = trim((string) ($filters['q'] ?? ''));
        $perPage = (int) ($filters['feed_per_page'] ?? 25);
        $perPage = max(10, min($perPage, 60));
        $page = max(1, (int) ($filters['page'] ?? 1));

        return [
            'source' => $source,
            'module' => $module,
            'type' => $type,
            'feed_q' => $feedQuery,
            'global_q' => $globalQuery,
            'per_page' => $perPage,
            'page' => $page,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function buildKpis(): array
    {
        $today = now()->toDateString();

        $clipsPublished = Clip::query()->where('is_published', true)->count();
        $clipsDraft = Clip::query()->where('is_published', false)->count();

        $matchesOpen = EsportMatch::query()
            ->whereIn('status', [EsportMatch::STATUS_SCHEDULED, EsportMatch::STATUS_LOCKED])
            ->count();
        $matchesLive = EsportMatch::query()->where('status', EsportMatch::STATUS_LIVE)->count();
        $matchesToClose = EsportMatch::query()
            ->whereIn('status', [
                EsportMatch::STATUS_SCHEDULED,
                EsportMatch::STATUS_LOCKED,
                EsportMatch::STATUS_LIVE,
                EsportMatch::STATUS_FINISHED,
            ])
            ->whereNull('settled_at')
            ->where('starts_at', '<=', now())
            ->count();

        $betsPending = Bet::query()
            ->whereIn('status', [Bet::STATUS_PENDING, Bet::STATUS_PLACED])
            ->count();

        $betsToSettle = Bet::query()
            ->whereIn('status', [Bet::STATUS_PENDING, Bet::STATUS_PLACED])
            ->whereHas('match', function ($query): void {
                $query->whereNotNull('result')
                    ->orWhereIn('status', [EsportMatch::STATUS_FINISHED, EsportMatch::STATUS_SETTLED]);
            })
            ->count();

        $giftRedemptionsPending = GiftRedemption::query()
            ->where('status', GiftRedemption::STATUS_PENDING)
            ->count();
        $giftRedemptionsApproved = GiftRedemption::query()
            ->where('status', GiftRedemption::STATUS_APPROVED)
            ->count();
        $giftRedemptionsShipped = GiftRedemption::query()
            ->where('status', GiftRedemption::STATUS_SHIPPED)
            ->count();

        $pointsVolumeToday = (int) RewardWalletTransaction::query()
            ->whereDate('created_at', $today)
            ->sum(DB::raw('abs(amount)'));

        $shopPurchasesToday = $this->hasTable('user_purchases')
            ? UserPurchase::query()->whereDate('purchased_at', $today)->count()
            : 0;

        $giftRedemptionsToday = GiftRedemption::query()
            ->whereDate('requested_at', $today)
            ->count();

        $reviewsPending = $this->hasTable('club_reviews')
            ? ClubReview::query()->where('status', ClubReview::STATUS_DRAFT)->count()
            : 0;

        $supportersActive = $this->hasTable('user_support_subscriptions')
            ? UserSupportSubscription::query()->active()->count()
            : 0;

        $lowStockGifts = Gift::query()
            ->where('is_active', true)
            ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->count();

        $lowStockShopItems = $this->hasTable('shop_items')
            ? ShopItem::query()
                ->where('is_active', true)
                ->whereNotNull('stock')
                ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
                ->count()
            : 0;

        return [
            'users_total' => (int) User::query()->count(),
            'users_recent' => (int) User::query()->where('created_at', '>=', now()->subDays(7))->count(),
            'admins_total' => (int) User::query()->where('role', User::ROLE_ADMIN)->count(),
            'clips_published' => (int) $clipsPublished,
            'clips_draft' => (int) $clipsDraft,
            'matches_open' => (int) $matchesOpen,
            'matches_live' => (int) $matchesLive,
            'matches_to_close' => (int) $matchesToClose,
            'bets_pending' => (int) $betsPending,
            'bets_to_settle' => (int) $betsToSettle,
            'gift_redemptions_pending' => (int) $giftRedemptionsPending,
            'gift_redemptions_approved' => (int) $giftRedemptionsApproved,
            'gift_redemptions_shipped' => (int) $giftRedemptionsShipped,
            'points_volume_today' => (int) $pointsVolumeToday,
            'shop_purchases_today' => (int) $shopPurchasesToday,
            'gift_redemptions_today' => (int) $giftRedemptionsToday,
            'reviews_pending' => (int) $reviewsPending,
            'supporters_active' => (int) $supportersActive,
            'low_stock_total' => (int) ($lowStockGifts + $lowStockShopItems),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPendingOperations(int $limit = 6): array
    {
        $redemptionsPending = GiftRedemption::query()
            ->with(['user:id,name,email', 'gift:id,title,is_active,stock'])
            ->where('status', GiftRedemption::STATUS_PENDING)
            ->orderBy('requested_at')
            ->limit($limit)
            ->get();

        $redemptionsApproved = GiftRedemption::query()
            ->with(['user:id,name,email', 'gift:id,title,is_active,stock'])
            ->where('status', GiftRedemption::STATUS_APPROVED)
            ->orderBy('approved_at')
            ->limit($limit)
            ->get();

        $redemptionsShipped = GiftRedemption::query()
            ->with(['user:id,name,email', 'gift:id,title,is_active,stock'])
            ->where('status', GiftRedemption::STATUS_SHIPPED)
            ->orderBy('shipped_at')
            ->limit($limit)
            ->get();

        $matchesToSettle = EsportMatch::query()
            ->whereIn('status', [
                EsportMatch::STATUS_SCHEDULED,
                EsportMatch::STATUS_LOCKED,
                EsportMatch::STATUS_LIVE,
                EsportMatch::STATUS_FINISHED,
            ])
            ->whereNull('settled_at')
            ->where('starts_at', '<=', now())
            ->orderBy('starts_at')
            ->limit($limit)
            ->get([
                'id',
                'event_name',
                'competition_name',
                'team_a_name',
                'team_b_name',
                'starts_at',
                'status',
            ]);

        $reviewsPending = $this->hasTable('club_reviews')
            ? ClubReview::query()
                ->with('user:id,name,email')
                ->where('status', ClubReview::STATUS_DRAFT)
                ->orderBy('created_at')
                ->limit($limit)
                ->get(['id', 'user_id', 'content', 'status', 'created_at'])
            : collect();

        $lowStockGifts = Gift::query()
            ->where('is_active', true)
            ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->orderBy('stock')
            ->orderBy('id')
            ->limit($limit)
            ->get(['id', 'title', 'stock', 'is_active', 'cost_points']);

        $lowStockShopItems = $this->hasTable('shop_items')
            ? ShopItem::query()
                ->where('is_active', true)
                ->whereNotNull('stock')
                ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
                ->orderBy('stock')
                ->orderBy('id')
                ->limit($limit)
                ->get(['id', 'key', 'name', 'stock', 'is_active', 'cost_points'])
            : collect();

        $betsToSettleCount = Bet::query()
            ->whereIn('status', [Bet::STATUS_PENDING, Bet::STATUS_PLACED])
            ->whereHas('match', function ($query): void {
                $query->whereNotNull('result')
                    ->orWhereIn('status', [EsportMatch::STATUS_FINISHED, EsportMatch::STATUS_SETTLED]);
            })
            ->count();

        return [
            'counts' => [
                'redemptions_pending' => GiftRedemption::query()
                    ->where('status', GiftRedemption::STATUS_PENDING)
                    ->count(),
                'redemptions_approved' => GiftRedemption::query()
                    ->where('status', GiftRedemption::STATUS_APPROVED)
                    ->count(),
                'redemptions_shipped' => GiftRedemption::query()
                    ->where('status', GiftRedemption::STATUS_SHIPPED)
                    ->count(),
                'matches_to_settle' => EsportMatch::query()
                    ->whereIn('status', [
                        EsportMatch::STATUS_SCHEDULED,
                        EsportMatch::STATUS_LOCKED,
                        EsportMatch::STATUS_LIVE,
                        EsportMatch::STATUS_FINISHED,
                    ])
                    ->whereNull('settled_at')
                    ->where('starts_at', '<=', now())
                    ->count(),
                'bets_to_settle' => (int) $betsToSettleCount,
                'reviews_pending' => $this->hasTable('club_reviews')
                    ? ClubReview::query()->where('status', ClubReview::STATUS_DRAFT)->count()
                    : 0,
                'low_stock_gifts' => Gift::query()
                    ->where('is_active', true)
                    ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
                    ->count(),
                'low_stock_shop_items' => $this->hasTable('shop_items')
                    ? ShopItem::query()
                        ->where('is_active', true)
                        ->whereNotNull('stock')
                        ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
                        ->count()
                    : 0,
            ],
            'redemptions_pending' => $redemptionsPending,
            'redemptions_approved' => $redemptionsApproved,
            'redemptions_shipped' => $redemptionsShipped,
            'matches_to_settle' => $matchesToSettle,
            'reviews_pending' => $reviewsPending,
            'low_stock_gifts' => $lowStockGifts,
            'low_stock_shop_items' => $lowStockShopItems,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildAlerts(): array
    {
        $pendingTooLong = GiftRedemption::query()
            ->where('status', GiftRedemption::STATUS_PENDING)
            ->where('requested_at', '<=', now()->subHours(self::LONG_PENDING_HOURS))
            ->count();

        $shippedWithoutTracking = GiftRedemption::query()
            ->where('status', GiftRedemption::STATUS_SHIPPED)
            ->where(function ($query): void {
                $query->whereNull('tracking_code')
                    ->orWhere('tracking_code', '');
            })
            ->count();

        $giftLowStock = Gift::query()
            ->where('is_active', true)
            ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->count();
        $giftOutOfStock = Gift::query()
            ->where('is_active', true)
            ->where('stock', '<=', 0)
            ->count();

        $shopLowStock = $this->hasTable('shop_items')
            ? ShopItem::query()
                ->where('is_active', true)
                ->whereNotNull('stock')
                ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
                ->count()
            : 0;
        $shopOutOfStock = $this->hasTable('shop_items')
            ? ShopItem::query()
                ->where('is_active', true)
                ->whereNotNull('stock')
                ->where('stock', '<=', 0)
                ->count()
            : 0;

        $matchesPastCutoff = EsportMatch::query()
            ->whereIn('status', [
                EsportMatch::STATUS_SCHEDULED,
                EsportMatch::STATUS_LOCKED,
                EsportMatch::STATUS_LIVE,
            ])
            ->where('starts_at', '<=', now()->subMinutes(30))
            ->count();

        $betsResultKnownNotSettled = Bet::query()
            ->whereIn('status', [Bet::STATUS_PENDING, Bet::STATUS_PLACED])
            ->whereHas('match', function ($query): void {
                $query->whereNotNull('result')
                    ->orWhere('status', EsportMatch::STATUS_FINISHED);
            })
            ->count();

        $negativeBetWallets = $this->hasTable('user_wallets')
            ? UserWallet::query()->where('balance', '<', 0)->count()
            : 0;
        $negativeRewardWallets = $this->hasTable('user_reward_wallets')
            ? UserRewardWallet::query()->where('balance', '<', 0)->count()
            : 0;
        $abnormalTransactions = RewardWalletTransaction::query()
            ->whereDate('created_at', now()->toDateString())
            ->whereRaw('abs(amount) >= ?', [200000])
            ->count();

        $moderationReviews = $this->hasTable('club_reviews')
            ? ClubReview::query()->where('status', ClubReview::STATUS_DRAFT)->count()
            : 0;

        return [
            [
                'code' => 'gift_pending_too_long',
                'severity' => 'critical',
                'title' => 'Cadeaux en attente trop longtemps',
                'description' => 'Demandes pendantes depuis plus de '.self::LONG_PENDING_HOURS.'h.',
                'count' => (int) $pendingTooLong,
                'url' => route('admin.gifts.index', [
                    'status' => GiftRedemption::STATUS_PENDING,
                    'sort' => 'requested_asc',
                ]).'#gift-redemptions-center',
            ],
            [
                'code' => 'gift_shipped_without_tracking',
                'severity' => 'warning',
                'title' => 'Expeditions sans tracking',
                'description' => 'Demandes expediees sans code de suivi renseigne.',
                'count' => (int) $shippedWithoutTracking,
                'url' => route('admin.gifts.index', [
                    'status' => GiftRedemption::STATUS_SHIPPED,
                    'sort' => 'updated_desc',
                ]).'#gift-redemptions-center',
            ],
            [
                'code' => 'gift_stock_low',
                'severity' => $giftOutOfStock > 0 ? 'critical' : 'warning',
                'title' => 'Stock cadeaux faible / rupture',
                'description' => 'Rupture: '.$giftOutOfStock.' | Faible: '.$giftLowStock,
                'count' => (int) $giftLowStock,
                'url' => route('admin.gifts.index').'#gift-stock-alerts',
            ],
            [
                'code' => 'shop_stock_low',
                'severity' => $shopOutOfStock > 0 ? 'critical' : 'warning',
                'title' => 'Stock shop faible / rupture',
                'description' => 'Rupture: '.$shopOutOfStock.' | Faible: '.$shopLowStock,
                'count' => (int) $shopLowStock,
                'url' => route('admin.dashboard').'#ops-low-stock-shop',
            ],
            [
                'code' => 'matches_past_cutoff',
                'severity' => 'critical',
                'title' => 'Matchs a traiter',
                'description' => 'Matchs a heure passee encore non traites.',
                'count' => (int) $matchesPastCutoff,
                'url' => route('admin.dashboard').'#ops-matches-to-settle',
            ],
            [
                'code' => 'bets_not_settled',
                'severity' => 'critical',
                'title' => 'Paris non settles',
                'description' => 'Paris en attente alors qu un résultat existe deja.',
                'count' => (int) $betsResultKnownNotSettled,
                'url' => route('admin.dashboard', ['feed_module' => 'bets']).'#admin-feed',
            ],
            [
                'code' => 'wallet_anomaly',
                'severity' => 'warning',
                'title' => 'Anomalies wallet detectees',
                'description' => 'Wallets negatifs: '.($negativeBetWallets + $negativeRewardWallets).' | Mouvements anormaux: '.$abnormalTransactions,
                'count' => (int) ($negativeBetWallets + $negativeRewardWallets + $abnormalTransactions),
                'url' => route('admin.wallets.grant.create'),
            ],
            [
                'code' => 'content_moderation',
                'severity' => 'warning',
                'title' => 'Contenu a moderer rapidement',
                'description' => 'Avis en brouillon a valider/masquer.',
                'count' => (int) $moderationReviews,
                'url' => route('admin.reviews.index', ['status' => ClubReview::STATUS_DRAFT]),
            ],
        ];
    }

    private function buildFeed(
        string $source,
        string $module,
        string $type,
        string $search,
        int $perPage,
        int $page
    ): LengthAwarePaginator {
        $feed = collect();

        if ($source === 'all' || $source === 'audit') {
            $feed = $feed->merge($this->collectAuditFeed(limit: 260));
        }

        if ($source === 'all' || $source === 'activity') {
            $feed = $feed->merge($this->collectActivityFeed(limit: 180));
        }

        if ($module !== 'all') {
            $feed = $feed->filter(fn (array $item): bool => $item['module_key'] === $module);
        }

        if ($type !== 'all') {
            $feed = $feed->filter(fn (array $item): bool => $item['type_key'] === $type);
        }

        if ($search !== '') {
            $needle = Str::lower($search);
            $feed = $feed->filter(function (array $item) use ($needle): bool {
                $haystack = Str::lower(implode(' ', [
                    (string) ($item['type_label'] ?? ''),
                    (string) ($item['module_label'] ?? ''),
                    (string) ($item['summary'] ?? ''),
                    (string) ($item['user_label'] ?? ''),
                    (string) ($item['target_label'] ?? ''),
                    (string) ($item['event_key'] ?? ''),
                ]));

                return Str::contains($haystack, $needle);
            });
        }

        $feed = $feed
            ->sortByDesc(function (array $item): float {
                return ((float) ($item['occurred_at_ts'] ?? 0) * 100000) + (float) ($item['sort_id'] ?? 0);
            })
            ->values();

        $total = $feed->count();
        $items = $feed->forPage($page, $perPage)->values()->all();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => route('admin.dashboard'),
                'query' => request()->query(),
            ]
        );
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function collectAuditFeed(int $limit): Collection
    {
        if (! $this->hasTable('audit_logs')) {
            return collect();
        }

        return AuditLog::query()
            ->with(['actor', 'target'])
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (AuditLog $log): ?array => $this->normalizeAuditFeedItem($log))
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function collectActivityFeed(int $limit): Collection
    {
        if (! $this->hasTable('activity_events')) {
            return collect();
        }

        return ActivityEvent::query()
            ->with('user:id,name,email')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (ActivityEvent $event): ?array => $this->normalizeActivityFeedItem($event))
            ->filter()
            ->values();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function normalizeAuditFeedItem(AuditLog $log): ?array
    {
        $context = is_array($log->context) ? $log->context : [];
        $actionMeta = $this->auditActionMeta((string) $log->action, $context);

        if ($actionMeta === null) {
            return null;
        }

        $target = $this->resolveTargetDescriptor(
            target: $log->target,
            targetType: $log->target_type,
            targetId: $log->target_id,
            context: $context,
            module: $actionMeta['module_key'],
        );

        $userLabel = 'Systeme';
        $userUrl = null;
        if ($log->actor instanceof User) {
            $userLabel = $log->actor->name.' (#'.$log->actor->id.')';
            $userUrl = route('users.index', ['user_id' => $log->actor->id]);
        } elseif ($log->actor_type !== null && $log->actor_id !== null) {
            $userLabel = class_basename((string) $log->actor_type).' #'.$log->actor_id;
        }

        $summary = $this->buildAuditSummary($actionMeta['label'], $context);

        return [
            'source_key' => 'audit',
            'source_label' => 'Audit',
            'sort_id' => (int) $log->id,
            'event_key' => 'audit:'.$log->id,
            'type_key' => (string) $actionMeta['type_key'],
            'type_label' => (string) $actionMeta['label'],
            'module_key' => (string) $actionMeta['module_key'],
            'module_label' => (string) $actionMeta['module_label'],
            'occurred_at_ts' => (int) optional($log->created_at)->timestamp,
            'occurred_at_label' => optional($log->created_at)->format('d/m/Y H:i:s') ?? '-',
            'user_label' => $userLabel,
            'user_url' => $userUrl,
            'target_label' => $target['label'],
            'target_url' => $target['url'],
            'summary' => $summary,
            'detail_url' => $target['url'] ?? route('admin.dashboard', ['feed_type' => $actionMeta['type_key']]),
            'primary_action' => $this->resolvePrimaryAction(
                typeKey: (string) $actionMeta['type_key'],
                targetType: (string) ($log->target_type ?? ''),
                targetId: (int) ($log->target_id ?? 0),
            ),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function normalizeActivityFeedItem(ActivityEvent $event): ?array
    {
        $eventType = strtolower(trim((string) $event->event_type));

        $activityMeta = match ($eventType) {
            ActivityEvent::TYPE_CLIP_LIKE => [
                'module_key' => 'clips',
                'module_label' => 'Clips',
                'type_key' => 'activity.clip_like',
                'label' => 'Like clip',
            ],
            ActivityEvent::TYPE_CLIP_COMMENT => [
                'module_key' => 'clips',
                'module_label' => 'Clips',
                'type_key' => 'activity.clip_comment',
                'label' => 'Commentaire clip',
            ],
            ActivityEvent::TYPE_CLIP_FAVORITE => [
                'module_key' => 'clips',
                'module_label' => 'Clips',
                'type_key' => 'activity.clip_favorite',
                'label' => 'Favori clip',
            ],
            ActivityEvent::TYPE_CLIP_SHARE => [
                'module_key' => 'clips',
                'module_label' => 'Clips',
                'type_key' => 'activity.clip_share',
                'label' => 'Partage clip',
            ],
            ActivityEvent::TYPE_BET_PLACED => [
                'module_key' => 'bets',
                'module_label' => 'Paris',
                'type_key' => 'activity.bet_placed',
                'label' => 'Pari place',
            ],
            ActivityEvent::TYPE_BET_WON => [
                'module_key' => 'bets',
                'module_label' => 'Paris',
                'type_key' => 'activity.bet_won',
                'label' => 'Pari gagne',
            ],
            ActivityEvent::TYPE_DUEL_SENT => [
                'module_key' => 'duels',
                'module_label' => 'Duels',
                'type_key' => 'activity.duel_sent',
                'label' => 'Duel cree',
            ],
            ActivityEvent::TYPE_DUEL_ACCEPTED => [
                'module_key' => 'duels',
                'module_label' => 'Duels',
                'type_key' => 'activity.duel_accepted',
                'label' => 'Duel accepte',
            ],
            ActivityEvent::TYPE_LOGIN_DAILY => [
                'module_key' => 'users',
                'module_label' => 'Utilisateurs',
                'type_key' => 'activity.login_daily',
                'label' => 'Connexion quotidienne',
            ],
            default => null,
        };

        if ($activityMeta === null) {
            return null;
        }

        $userLabel = $event->user?->name
            ? $event->user->name.' (#'.$event->user_id.')'
            : 'Utilisateur #'.$event->user_id;

        $targetLabel = strtoupper((string) $event->ref_type).' #'.$event->ref_id;
        $targetUrl = null;
        if ((string) $event->ref_type === 'match' && ctype_digit((string) $event->ref_id)) {
            $targetUrl = route('admin.matches.manage', (int) $event->ref_id);
        }
        if ((string) $event->ref_type === 'clip' && ctype_digit((string) $event->ref_id)) {
            $targetUrl = route('admin.clips.edit', (int) $event->ref_id);
        }

        return [
            'source_key' => 'activity',
            'source_label' => 'Activity',
            'sort_id' => (int) $event->id,
            'event_key' => 'activity:'.$event->id,
            'type_key' => (string) $activityMeta['type_key'],
            'type_label' => (string) $activityMeta['label'],
            'module_key' => (string) $activityMeta['module_key'],
            'module_label' => (string) $activityMeta['module_label'],
            'occurred_at_ts' => (int) optional($event->occurred_at)->timestamp,
            'occurred_at_label' => optional($event->occurred_at)->format('d/m/Y H:i:s') ?? '-',
            'user_label' => $userLabel,
            'user_url' => route('users.index', ['user_id' => $event->user_id]),
            'target_label' => $targetLabel,
            'target_url' => $targetUrl,
            'summary' => (string) $activityMeta['label'].' - '.$targetLabel,
            'detail_url' => $targetUrl ?: route('admin.dashboard', ['feed_source' => 'activity']),
            'primary_action' => null,
        ];
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, string>|null
     */
    private function auditActionMeta(string $action, array $context): ?array
    {
        return match (true) {
            $action === 'shop.purchase.completed' => [
                'module_key' => 'shop',
                'module_label' => 'Shop',
                'type_key' => 'shop.purchase.completed',
                'label' => 'Achat shop',
            ],
            $action === 'gift.redeem' => [
                'module_key' => 'gifts',
                'module_label' => 'Cadeaux',
                'type_key' => 'gift.redeem',
                'label' => 'Demande cadeau',
            ],
            $action === 'gift.redeem.approve' => [
                'module_key' => 'gifts',
                'module_label' => 'Cadeaux',
                'type_key' => 'gift.redeem.approve',
                'label' => 'Cadeau approuve',
            ],
            $action === 'gift.redeem.reject' => [
                'module_key' => 'gifts',
                'module_label' => 'Cadeaux',
                'type_key' => 'gift.redeem.reject',
                'label' => 'Cadeau rejete / rembourse',
            ],
            $action === 'gift.redeem.ship' => [
                'module_key' => 'gifts',
                'module_label' => 'Cadeaux',
                'type_key' => 'gift.redeem.ship',
                'label' => 'Cadeau expedie',
            ],
            $action === 'gift.redeem.deliver' => [
                'module_key' => 'gifts',
                'module_label' => 'Cadeaux',
                'type_key' => 'gift.redeem.deliver',
                'label' => 'Cadeau livre',
            ],
            Str::startsWith($action, 'wallet.') => [
                'module_key' => 'wallet',
                'module_label' => 'Wallet',
                'type_key' => $action,
                'label' => 'Operation wallet',
            ],
            Str::startsWith($action, 'platform.points.') => [
                'module_key' => 'wallet',
                'module_label' => 'Wallet',
                'type_key' => $action,
                'label' => 'Credit/débit points',
            ],
            $action === 'reward_wallet.grant' => [
                'module_key' => 'wallet',
                'module_label' => 'Wallet',
                'type_key' => 'reward_wallet.grant',
                'label' => 'Ajustement points',
            ],
            $action === 'bets.placed' => [
                'module_key' => 'bets',
                'module_label' => 'Paris',
                'type_key' => 'bets.placed',
                'label' => 'Pari place',
            ],
            $action === 'bets.cancelled' => [
                'module_key' => 'bets',
                'module_label' => 'Paris',
                'type_key' => 'bets.cancelled',
                'label' => 'Pari annule / rembourse',
            ],
            Str::startsWith($action, 'matches.') => [
                'module_key' => 'matches',
                'module_label' => 'Matchs',
                'type_key' => $action,
                'label' => $action === 'matches.settled' ? 'Settlement match' : 'Action match',
            ],
            Str::startsWith($action, 'duels.') => [
                'module_key' => 'duels',
                'module_label' => 'Duels',
                'type_key' => $action,
                'label' => match ($action) {
                    'duels.created' => 'Duel cree',
                    'duels.accepted' => 'Duel accepte',
                    'duels.result.recorded' => 'Duel termine',
                    default => 'Action duel',
                },
            ],
            Str::startsWith($action, 'clips.') => [
                'module_key' => 'clips',
                'module_label' => 'Clips',
                'type_key' => $action,
                'label' => match ($action) {
                    'clips.published' => 'Clip publie',
                    'clips.unpublished' => 'Clip depublie',
                    default => 'Action clip',
                },
            ],
            Str::startsWith($action, 'reviews.') => [
                'module_key' => 'reviews',
                'module_label' => 'Avis',
                'type_key' => $action,
                'label' => 'Action avis',
            ],
            Str::startsWith($action, 'missions.template.') => [
                'module_key' => 'missions',
                'module_label' => 'Missions',
                'type_key' => $action,
                'label' => 'Template mission',
            ],
            Str::startsWith($action, 'missions.generation.') => [
                'module_key' => 'missions',
                'module_label' => 'Missions',
                'type_key' => $action,
                'label' => 'Generation mission',
            ],
            $action === 'missions.repair.run' => [
                'module_key' => 'missions',
                'module_label' => 'Missions',
                'type_key' => 'missions.repair.run',
                'label' => 'Maintenance missions',
            ],
            $action === 'missions.progress.recorded' && ! empty($context['missions_completed']) => [
                'module_key' => 'missions',
                'module_label' => 'Missions',
                'type_key' => 'missions.progress.completed',
                'label' => 'Mission validee',
            ],
            $action === 'live-codes.redeemed' => [
                'module_key' => 'live_codes',
                'module_label' => 'Codes live',
                'type_key' => 'live-codes.redeemed',
                'label' => 'Code live utilise',
            ],
            Str::startsWith($action, 'live-codes.') => [
                'module_key' => 'live_codes',
                'module_label' => 'Codes live',
                'type_key' => $action,
                'label' => 'Action code live',
            ],
            Str::startsWith($action, 'gifts.') => [
                'module_key' => 'gifts',
                'module_label' => 'Cadeaux',
                'type_key' => $action,
                'label' => 'Action catalogue cadeau',
            ],
            Str::startsWith($action, 'shop.items.') => [
                'module_key' => 'shop',
                'module_label' => 'Shop',
                'type_key' => $action,
                'label' => 'Action article shop',
            ],
            default => null,
        };
    }

    /**
     * @param array<string, mixed> $context
     */
    private function buildAuditSummary(string $label, array $context): string
    {
        $parts = [$label];

        if (isset($context['match_id'])) {
            $parts[] = 'Match #'.$context['match_id'];
        }
        if (isset($context['redemption_id'])) {
            $parts[] = 'Redemption #'.$context['redemption_id'];
        }
        if (isset($context['gift_id'])) {
            $parts[] = 'Cadeau #'.$context['gift_id'];
        }
        if (isset($context['bet_id'])) {
            $parts[] = 'Pari #'.$context['bet_id'];
        }
        if (isset($context['duel_id'])) {
            $parts[] = 'Duel #'.$context['duel_id'];
        }
        if (isset($context['tracking_code']) && (string) $context['tracking_code'] !== '') {
            $parts[] = 'Tracking '.$context['tracking_code'];
        }
        if (isset($context['reason']) && (string) $context['reason'] !== '') {
            $parts[] = 'Motif '.$context['reason'];
        }
        if (isset($context['amount'])) {
            $parts[] = 'Montant '.(int) $context['amount'];
        }
        if (isset($context['stake_points'])) {
            $parts[] = 'Mise '.(int) $context['stake_points'];
        }

        return implode(' - ', $parts);
    }

    /**
     * @param array<string, mixed> $context
     * @return array{label: string, url: string|null}
     */
    private function resolveTargetDescriptor(
        ?Model $target,
        ?string $targetType,
        ?int $targetId,
        array $context,
        string $module
    ): array {
        if ($target instanceof User) {
            return [
                'label' => $target->name.' (#'.$target->id.')',
                'url' => route('users.index', ['user_id' => $target->id]),
            ];
        }

        if ($target instanceof GiftRedemption || $targetType === GiftRedemption::class) {
            $id = (int) ($target?->getKey() ?? $targetId);

            return [
                'label' => 'Redemption #'.$id,
                'url' => route('admin.gifts.index', ['status' => 'all']).'#redemption-'.$id,
            ];
        }

        if ($target instanceof Gift || $targetType === Gift::class) {
            $id = (int) ($target?->getKey() ?? $targetId);

            return [
                'label' => 'Cadeau #'.$id,
                'url' => route('admin.gifts.index').'#gift-'.$id,
            ];
        }

        if ($target instanceof ShopItem || $targetType === ShopItem::class) {
            $id = (int) ($target?->getKey() ?? $targetId);
            $name = $target instanceof ShopItem ? (string) $target->name : 'Article #'.$id;

            return [
                'label' => $name,
                'url' => route('admin.dashboard', ['q' => (string) $id]),
            ];
        }

        if ($target instanceof UserPurchase || $targetType === UserPurchase::class) {
            $id = (int) ($target?->getKey() ?? $targetId);

            return [
                'label' => 'Achat #'.$id,
                'url' => route('admin.dashboard', ['q' => (string) $id]),
            ];
        }

        if ($target instanceof EsportMatch || $targetType === EsportMatch::class) {
            $id = (int) ($target?->getKey() ?? $targetId);
            $title = $target instanceof EsportMatch ? $target->displayTitle() : 'Match #'.$id;

            return [
                'label' => $title,
                'url' => route('admin.matches.manage', $id),
            ];
        }

        if ($target instanceof Bet || $targetType === Bet::class) {
            $id = (int) ($target?->getKey() ?? $targetId);
            $matchId = (int) ($context['match_id'] ?? 0);

            return [
                'label' => 'Pari #'.$id,
                'url' => $matchId > 0 ? route('admin.matches.manage', $matchId) : route('admin.dashboard', ['q' => (string) $id]),
            ];
        }

        if ($target instanceof Clip || $targetType === Clip::class) {
            $id = (int) ($target?->getKey() ?? $targetId);
            $title = $target instanceof Clip ? (string) $target->title : 'Clip #'.$id;

            return [
                'label' => $title,
                'url' => route('admin.clips.edit', $id),
            ];
        }

        if ($target instanceof ClubReview || $targetType === ClubReview::class) {
            $id = (int) ($target?->getKey() ?? $targetId);

            return [
                'label' => 'Avis #'.$id,
                'url' => route('admin.reviews.index', ['q' => (string) $id]).'#review-'.$id,
            ];
        }

        if ($target instanceof Duel || $targetType === Duel::class) {
            $id = (int) ($target?->getKey() ?? $targetId);

            return [
                'label' => 'Duel #'.$id,
                'url' => route('duels.index'),
            ];
        }

        if ($target instanceof MissionTemplate || $targetType === MissionTemplate::class) {
            $id = (int) ($target?->getKey() ?? $targetId);
            $title = $target instanceof MissionTemplate ? (string) $target->title : 'Template #'.$id;

            return [
                'label' => $title,
                'url' => route('admin.missions.index', ['q' => (string) $id]),
            ];
        }

        if ($target instanceof LiveCode || $targetType === LiveCode::class) {
            $id = (int) ($target?->getKey() ?? $targetId);
            $title = $target instanceof LiveCode ? (string) $target->label : 'Code #'.$id;

            return [
                'label' => $title,
                'url' => route('admin.missions.index', ['q' => (string) $id]),
            ];
        }

        return [
            'label' => strtoupper($module).' #'.(int) ($targetId ?? 0),
            'url' => null,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolvePrimaryAction(string $typeKey, string $targetType, int $targetId): ?array
    {
        if ($targetId <= 0 || $targetType !== GiftRedemption::class) {
            return null;
        }

        if ($typeKey === 'gift.redeem') {
            return [
                'label' => 'Approuver',
                'method' => 'POST',
                'url' => route('admin.redemptions.approve', $targetId),
            ];
        }

        if ($typeKey === 'gift.redeem.ship') {
            return [
                'label' => 'Livrer',
                'method' => 'POST',
                'url' => route('admin.redemptions.deliver', $targetId),
            ];
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private function feedSourceOptions(): array
    {
        return [
            'all' => 'Toutes sources',
            'audit' => 'Audit logs',
            'activity' => 'Activity events',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function feedModuleOptions(): array
    {
        return [
            'all' => 'Tous modules',
            'users' => 'Utilisateurs',
            'clips' => 'Clips',
            'matches' => 'Matchs',
            'bets' => 'Paris',
            'wallet' => 'Wallet',
            'gifts' => 'Cadeaux',
            'shop' => 'Shop',
            'missions' => 'Missions',
            'duels' => 'Duels',
            'reviews' => 'Avis',
            'live_codes' => 'Codes live',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function feedTypeOptions(): array
    {
        return [
            'all' => 'Tous types',
            'gift.redeem' => 'Demande cadeau',
            'gift.redeem.approve' => 'Cadeau approuve',
            'gift.redeem.reject' => 'Cadeau rejete',
            'gift.redeem.ship' => 'Cadeau expedie',
            'gift.redeem.deliver' => 'Cadeau livre',
            'shop.purchase.completed' => 'Achat shop',
            'bets.placed' => 'Pari place',
            'matches.settled' => 'Settlement match',
            'duels.created' => 'Duel cree',
            'duels.accepted' => 'Duel accepte',
            'duels.result.recorded' => 'Duel termine',
            'clips.published' => 'Clip publie',
            'clips.unpublished' => 'Clip depublie',
            'reviews.created' => 'Avis cree',
            'reviews.moderated' => 'Avis modere',
            'reviews.deleted' => 'Avis retire',
            'reviews.deleted_by_admin' => 'Avis supprime admin',
            'live-codes.redeemed' => 'Code live utilise',
            'missions.template.created' => 'Template mission cree',
            'missions.template.updated' => 'Template mission maj',
            'missions.template.deleted' => 'Template mission supprime',
            'missions.generation.daily' => 'Generation daily',
            'missions.generation.weekly' => 'Generation weekly',
            'missions.generation.event_window' => 'Generation event window',
            'missions.repair.run' => 'Maintenance missions',
            'activity.clip_like' => 'Activity like clip',
            'activity.clip_comment' => 'Activity commentaire clip',
            'activity.bet_placed' => 'Activity pari place',
            'activity.duel_sent' => 'Activity duel cree',
        ];
    }

    /**
     * @param array<string, mixed> $kpis
     * @param array<string, mixed> $pending
     * @return array<int, array<string, mixed>>
     */
    private function quickLinks(array $kpis, array $pending): array
    {
        return [
            [
                'title' => 'Utilisateurs',
                'description' => 'Recherche comptes, roles et profils.',
                'route' => route('users.index'),
                'action' => 'Ouvrir users',
                'count' => (int) ($kpis['users_total'] ?? 0),
            ],
            [
                'title' => 'Cadeaux',
                'description' => 'Traitement demandes et stock catalogue.',
                'route' => route('admin.gifts.index'),
                'action' => 'Ouvrir cadeaux',
                'count' => (int) ($pending['counts']['redemptions_pending'] ?? 0),
            ],
            [
                'title' => 'Matchs',
                'description' => 'Suivi live, résultats et settlement.',
                'route' => route('admin.matches.index'),
                'action' => 'Ouvrir matchs',
                'count' => (int) ($pending['counts']['matches_to_settle'] ?? 0),
            ],
            [
                'title' => 'Wallet',
                'description' => 'Ajustements et anomalies points.',
                'route' => route('admin.wallets.grant.create'),
                'action' => 'Ouvrir wallet',
                'count' => (int) ($kpis['points_volume_today'] ?? 0),
            ],
            [
                'title' => 'Avis',
                'description' => 'Moderation et validation contenu.',
                'route' => route('admin.reviews.index'),
                'action' => 'Ouvrir avis',
                'count' => (int) ($pending['counts']['reviews_pending'] ?? 0),
            ],
            [
                'title' => 'Missions',
                'description' => 'Pilotage templates, live codes et events.',
                'route' => route('admin.missions.index'),
                'action' => 'Ouvrir missions',
                'count' => (int) ($kpis['supporters_active'] ?? 0),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildGlobalSearch(string $query): array
    {
        if ($query === '') {
            return [
                'query' => '',
                'total_hits' => 0,
                'groups' => [],
            ];
        }

        $needle = trim($query);
        $like = '%'.$needle.'%';
        $id = ctype_digit($needle) ? (int) $needle : null;
        $groups = [];

        $users = User::query()
            ->where(function ($builder) use ($id, $like): void {
                if ($id !== null) {
                    $builder->orWhere('id', $id);
                }

                $builder->orWhere('name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            })
            ->orderBy('id')
            ->limit(6)
            ->get(['id', 'name', 'email', 'role']);

        if ($users->isNotEmpty()) {
            $groups[] = [
                'title' => 'Utilisateurs',
                'items' => $users->map(fn (User $user): array => [
                    'title' => $user->name,
                    'meta' => '#'.$user->id.' - '.$user->email.' - '.$user->role,
                    'url' => route('users.index', ['user_id' => $user->id]),
                ])->all(),
            ];
        }

        $redemptions = GiftRedemption::query()
            ->with(['user:id,name,email', 'gift:id,title'])
            ->where(function ($builder) use ($id, $like): void {
                if ($id !== null) {
                    $builder->orWhere('id', $id);
                }

                $builder->orWhere('status', 'like', $like)
                    ->orWhereHas('user', fn ($query) => $query->where('email', 'like', $like)->orWhere('name', 'like', $like))
                    ->orWhereHas('gift', fn ($query) => $query->where('title', 'like', $like));
            })
            ->orderByDesc('id')
            ->limit(6)
            ->get(['id', 'user_id', 'gift_id', 'status', 'requested_at']);

        if ($redemptions->isNotEmpty()) {
            $groups[] = [
                'title' => 'Demandes cadeaux',
                'items' => $redemptions->map(fn (GiftRedemption $item): array => [
                    'title' => 'Redemption #'.$item->id.' - '.($item->gift?->title ?? 'cadeau'),
                    'meta' => ($item->user?->email ?? 'n/a').' - '.$item->status,
                    'url' => route('admin.gifts.index', ['status' => $item->status]).'#redemption-'.$item->id,
                ])->all(),
            ];
        }

        if ($this->hasTable('user_purchases')) {
            $purchases = UserPurchase::query()
                ->with(['user:id,name,email', 'shopItem:id,name,key'])
                ->where(function ($builder) use ($id, $like): void {
                    if ($id !== null) {
                        $builder->orWhere('id', $id);
                    }

                    $builder->orWhereHas('user', fn ($query) => $query->where('email', 'like', $like)->orWhere('name', 'like', $like))
                        ->orWhereHas('shopItem', fn ($query) => $query->where('name', 'like', $like)->orWhere('key', 'like', $like));
                })
                ->orderByDesc('id')
                ->limit(6)
                ->get(['id', 'user_id', 'shop_item_id', 'cost_points', 'status', 'purchased_at']);

            if ($purchases->isNotEmpty()) {
                $groups[] = [
                    'title' => 'Achats shop',
                    'items' => $purchases->map(fn (UserPurchase $item): array => [
                        'title' => 'Achat #'.$item->id.' - '.($item->shopItem?->name ?? 'article'),
                        'meta' => ($item->user?->email ?? 'n/a').' - '.$item->cost_points.' pts',
                        'url' => route('admin.dashboard', ['q' => (string) $item->id]),
                    ])->all(),
                ];
            }
        }

        $gifts = Gift::query()
            ->where(function ($builder) use ($id, $like): void {
                if ($id !== null) {
                    $builder->orWhere('id', $id);
                }

                $builder->orWhere('title', 'like', $like);
            })
            ->orderBy('id')
            ->limit(6)
            ->get(['id', 'title', 'stock', 'is_active']);

        if ($gifts->isNotEmpty()) {
            $groups[] = [
                'title' => 'Cadeaux',
                'items' => $gifts->map(fn (Gift $gift): array => [
                    'title' => $gift->title,
                    'meta' => '#'.$gift->id.' - Stock '.$gift->stock.' - '.($gift->is_active ? 'actif' : 'inactif'),
                    'url' => route('admin.gifts.index').'#gift-'.$gift->id,
                ])->all(),
            ];
        }

        if ($this->hasTable('shop_items')) {
            $shopItems = ShopItem::query()
                ->where(function ($builder) use ($id, $like): void {
                    if ($id !== null) {
                        $builder->orWhere('id', $id);
                    }

                    $builder->orWhere('name', 'like', $like)
                        ->orWhere('key', 'like', $like);
                })
                ->orderBy('id')
                ->limit(6)
                ->get(['id', 'key', 'name', 'stock', 'is_active']);

            if ($shopItems->isNotEmpty()) {
                $groups[] = [
                    'title' => 'Articles shop',
                    'items' => $shopItems->map(fn (ShopItem $item): array => [
                        'title' => $item->name,
                        'meta' => '#'.$item->id.' - '.$item->key.' - Stock '.($item->stock ?? 'infini'),
                        'url' => route('admin.dashboard', ['q' => (string) $item->id]),
                    ])->all(),
                ];
            }
        }

        $matches = EsportMatch::query()
            ->where(function ($builder) use ($id, $like): void {
                if ($id !== null) {
                    $builder->orWhere('id', $id);
                }

                $builder->orWhere('event_name', 'like', $like)
                    ->orWhere('competition_name', 'like', $like)
                    ->orWhere('team_a_name', 'like', $like)
                    ->orWhere('team_b_name', 'like', $like);
            })
            ->orderByDesc('starts_at')
            ->limit(6)
            ->get(['id', 'event_name', 'competition_name', 'team_a_name', 'team_b_name', 'status', 'starts_at']);

        if ($matches->isNotEmpty()) {
            $groups[] = [
                'title' => 'Matchs',
                'items' => $matches->map(fn (EsportMatch $match): array => [
                    'title' => $match->displayTitle(),
                    'meta' => '#'.$match->id.' - '.$match->status,
                    'url' => route('admin.matches.manage', $match->id),
                ])->all(),
            ];
        }

        $clips = Clip::query()
            ->where(function ($builder) use ($id, $like): void {
                if ($id !== null) {
                    $builder->orWhere('id', $id);
                }

                $builder->orWhere('title', 'like', $like)
                    ->orWhere('slug', 'like', $like);
            })
            ->orderByDesc('id')
            ->limit(6)
            ->get(['id', 'title', 'slug', 'is_published']);

        if ($clips->isNotEmpty()) {
            $groups[] = [
                'title' => 'Clips',
                'items' => $clips->map(fn (Clip $clip): array => [
                    'title' => $clip->title,
                    'meta' => '#'.$clip->id.' - '.$clip->slug.' - '.($clip->is_published ? 'publie' : 'brouillon'),
                    'url' => route('admin.clips.edit', $clip->id),
                ])->all(),
            ];
        }

        if ($this->hasTable('club_reviews')) {
            $reviews = ClubReview::query()
                ->with('user:id,name,email')
                ->where(function ($builder) use ($id, $like): void {
                    if ($id !== null) {
                        $builder->orWhere('id', $id);
                    }

                    $builder->orWhere('content', 'like', $like)
                        ->orWhereHas('user', fn ($query) => $query->where('email', 'like', $like)->orWhere('name', 'like', $like));
                })
                ->orderByDesc('id')
                ->limit(6)
                ->get(['id', 'user_id', 'author_name', 'status', 'content']);

            if ($reviews->isNotEmpty()) {
                $groups[] = [
                    'title' => 'Avis',
                    'items' => $reviews->map(fn (ClubReview $review): array => [
                        'title' => 'Avis #'.$review->id.' - '.$review->authorDisplayName(),
                        'meta' => $review->status.' - '.Str::limit((string) $review->content, 80),
                        'url' => route('admin.reviews.index', ['q' => (string) $review->id]).'#review-'.$review->id,
                    ])->all(),
                ];
            }
        }

        if ($this->hasTable('user_support_subscriptions')) {
            $supporters = UserSupportSubscription::query()
                ->select('user_support_subscriptions.*')
                ->whereIn(
                    'id',
                    UserSupportSubscription::query()
                        ->selectRaw('MAX(id)')
                        ->groupBy('user_id')
                )
                ->with('user:id,name,email')
                ->whereHas('user', function ($builder) use ($id, $like): void {
                    if ($id !== null) {
                        $builder->orWhere('users.id', $id);
                    }

                    $builder->orWhere('name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                })
                ->orderByDesc('id')
                ->limit(6)
                ->get(['id', 'user_id', 'status', 'started_at', 'current_period_end']);

            if ($supporters->isNotEmpty()) {
                $groups[] = [
                    'title' => 'Supporters',
                    'items' => $supporters->map(fn (UserSupportSubscription $subscription): array => [
                        'title' => $subscription->user?->name ?? ('User #'.$subscription->user_id),
                        'meta' => '#'.$subscription->user_id.' - '.$subscription->status,
                        'url' => route('admin.supporters.show', $subscription->user_id),
                    ])->all(),
                ];
            }
        }

        return [
            'query' => $query,
            'total_hits' => (int) collect($groups)->sum(fn (array $group): int => count($group['items'] ?? [])),
            'groups' => $groups,
        ];
    }

    private function hasTable(string $table): bool
    {
        if (array_key_exists($table, $this->tablePresence)) {
            return $this->tablePresence[$table];
        }

        $this->tablePresence[$table] = Schema::hasTable($table);

        return $this->tablePresence[$table];
    }
}
