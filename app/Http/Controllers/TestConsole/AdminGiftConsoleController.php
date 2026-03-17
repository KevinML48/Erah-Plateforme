<?php

namespace App\Http\Controllers\TestConsole;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Application\Actions\Rewards\ApplyRewardWalletTransactionAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Console\RefundGiftRedemptionRequest;
use App\Http\Requests\Web\Console\RejectGiftRedemptionRequest;
use App\Http\Requests\Web\Console\ShipGiftRedemptionRequest;
use App\Http\Requests\Web\Console\StoreGiftConsoleRequest;
use App\Http\Requests\Web\Console\UpdateGiftConsoleRequest;
use App\Http\Requests\Web\Console\UpdateGiftRedemptionInternalNoteRequest;
use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\GiftRedemptionEvent;
use App\Models\RewardWalletTransaction;
use App\Support\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class AdminGiftConsoleController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'all');
        $statuses = GiftRedemption::statuses();
        if ($status !== 'all' && ! in_array($status, $statuses, true)) {
            $status = 'all';
        }

        $search = trim((string) $request->query('search', ''));
        $sort = (string) $request->query('sort', 'requested_desc');
        $giftIdFilterRaw = (string) $request->query('gift_id', '');
        $userIdFilterRaw = (string) $request->query('user_id', '');
        $giftIdFilter = is_numeric($giftIdFilterRaw) ? (int) $giftIdFilterRaw : null;
        $userIdFilter = is_numeric($userIdFilterRaw) ? (int) $userIdFilterRaw : null;

        $gifts = Gift::query()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(16, ['*'], 'gifts_page')
            ->withQueryString();

        $redemptionsQuery = GiftRedemption::query()
            ->with(['user:id,name,email', 'gift:id,title'])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($giftIdFilter !== null, fn ($query) => $query->where('gift_id', $giftIdFilter))
            ->when($userIdFilter !== null, fn ($query) => $query->where('user_id', $userIdFilter));

        if ($search !== '') {
            $redemptionsQuery->where(function ($query) use ($search): void {
                $query
                    ->where('tracking_code', 'like', '%'.$search.'%')
                    ->orWhere('tracking_carrier', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery
                            ->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('gift', fn ($giftQuery) => $giftQuery->where('title', 'like', '%'.$search.'%'));

                if (is_numeric($search)) {
                    $query->orWhereKey((int) $search);
                }
            });
        }

        match ($sort) {
            'requested_asc' => $redemptionsQuery->orderBy('requested_at'),
            'status' => $redemptionsQuery->orderBy('status')->orderByDesc('requested_at'),
            'updated_desc' => $redemptionsQuery->orderByDesc('updated_at'),
            default => $redemptionsQuery->orderByDesc('requested_at'),
        };

        $redemptions = $redemptionsQuery
            ->paginate(25, ['*'], 'redemptions_page')
            ->withQueryString();

        $inProgressPool = GiftRedemption::query()
            ->with(['user:id,name,email', 'gift:id,title'])
            ->whereIn('status', [
                GiftRedemption::STATUS_PENDING,
                GiftRedemption::STATUS_APPROVED,
                GiftRedemption::STATUS_SHIPPED,
            ])
            ->when($giftIdFilter !== null, fn ($query) => $query->where('gift_id', $giftIdFilter))
            ->when($userIdFilter !== null, fn ($query) => $query->where('user_id', $userIdFilter))
            ->orderByRaw("CASE status WHEN '".GiftRedemption::STATUS_PENDING."' THEN 0 WHEN '".GiftRedemption::STATUS_APPROVED."' THEN 1 WHEN '".GiftRedemption::STATUS_SHIPPED."' THEN 2 ELSE 9 END")
            ->orderByDesc('requested_at')
            ->get();

        $inProgressRedemptions = $inProgressPool
            ->take(20)
            ->values();

        $inProgressUserIds = $inProgressPool
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->values();

        $historicalOrdersByUser = $inProgressUserIds->isNotEmpty()
            ? GiftRedemption::query()
                ->whereIn('user_id', $inProgressUserIds)
                ->select('user_id', DB::raw('count(*) as aggregate'))
                ->groupBy('user_id')
                ->pluck('aggregate', 'user_id')
            : collect();

        $inProgressByUser = $inProgressPool
            ->groupBy('user_id')
            ->map(function ($rows, $userId) use ($historicalOrdersByUser): array {
                $first = $rows->first();
                $latestRequestedAt = $rows
                    ->pluck('requested_at')
                    ->filter()
                    ->sortDesc()
                    ->first();

                return [
                    'user_id' => (int) $userId,
                    'user' => $first?->user,
                    'active_orders' => $rows->count(),
                    'pending_orders' => $rows->where('status', GiftRedemption::STATUS_PENDING)->count(),
                    'approved_orders' => $rows->where('status', GiftRedemption::STATUS_APPROVED)->count(),
                    'shipped_orders' => $rows->where('status', GiftRedemption::STATUS_SHIPPED)->count(),
                    'total_points_in_progress' => (int) $rows->sum(fn (GiftRedemption $redemption) => (int) ($redemption->cost_points_snapshot ?? 0)),
                    'historical_orders' => (int) ($historicalOrdersByUser[(int) $userId] ?? 0),
                    'gift_titles' => $rows
                        ->map(fn (GiftRedemption $redemption) => $redemption->gift?->title)
                        ->filter()
                        ->unique()
                        ->values()
                        ->all(),
                    'latest_requested_at' => $latestRequestedAt,
                ];
            })
            ->sortByDesc('active_orders')
            ->values()
            ->take(12);

        $statusCounts = GiftRedemption::query()
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $stockAlerts = Gift::query()
            ->where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->orderBy('title')
            ->limit(12)
            ->get();

        $hasGiftFavoritesTable = Schema::hasTable('gift_favorites');
        $hasGiftCartItemsTable = Schema::hasTable('gift_cart_items');

        $mostFavorited = $hasGiftFavoritesTable
            ? Gift::query()
                ->withCount('favorites')
                ->orderByDesc('favorites_count')
                ->orderBy('title')
                ->limit(6)
                ->get()
            : collect();

        $mostAddedToCart = $hasGiftCartItemsTable
            ? Gift::query()
                ->withSum('cartItems as cart_quantity_total', 'quantity')
                ->orderByDesc('cart_quantity_total')
                ->orderBy('title')
                ->limit(6)
                ->get()
            : collect();

        $mostRequested = Gift::query()
            ->withCount('redemptions')
            ->orderByDesc('redemptions_count')
            ->orderBy('title')
            ->limit(6)
            ->get();

        $kpis = [
            'gifts_total' => Gift::query()->count(),
            'gifts_active' => Gift::query()->where('is_active', true)->count(),
            'pending_redemptions' => (int) ($statusCounts[GiftRedemption::STATUS_PENDING] ?? 0),
            'approved_redemptions' => (int) ($statusCounts[GiftRedemption::STATUS_APPROVED] ?? 0),
            'shipped_redemptions' => (int) ($statusCounts[GiftRedemption::STATUS_SHIPPED] ?? 0),
            'delivered_redemptions' => (int) ($statusCounts[GiftRedemption::STATUS_DELIVERED] ?? 0),
            'low_stock_gifts' => $stockAlerts->count(),
        ];

        return view('pages.admin.gifts.index', [
            'gifts' => $gifts,
            'redemptions' => $redemptions,
            'status' => $status,
            'statuses' => $statuses,
            'statusLabels' => GiftRedemption::statusLabels(),
            'search' => $search,
            'sort' => $sort,
            'giftIdFilter' => $giftIdFilterRaw,
            'userIdFilter' => $userIdFilterRaw,
            'kpis' => $kpis,
            'stockAlerts' => $stockAlerts,
            'inProgressRedemptions' => $inProgressRedemptions,
            'inProgressByUser' => $inProgressByUser,
            'mostFavorited' => $mostFavorited,
            'mostAddedToCart' => $mostAddedToCart,
            'mostRequested' => $mostRequested,
        ]);
    }

    public function store(
        StoreGiftConsoleRequest $request,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $validated = $request->validated();
        $gift = Gift::query()->create([
                'slug' => $this->resolveGiftSlug($validated['title'], $validated['slug'] ?? null),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
                'category' => $validated['category'] ?? null,
                'type' => $validated['type'] ?? null,
                'delivery_type' => $validated['delivery_type'] ?? null,
            'image_url' => $this->resolveImageUrl($request),
            'cost_points' => (int) $validated['cost_points'],
            'stock' => (int) $validated['stock'],
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
                'requires_admin_validation' => $request->boolean('requires_admin_validation', false),
                'metadata' => $this->buildGiftMetadata($validated, $request),
        ]);

        $storeAuditLogAction->execute(
            action: 'gifts.created',
            actor: $request->user(),
            target: $gift,
            context: [
                'gift_id' => $gift->id,
                'stock' => (int) $gift->stock,
                'cost_points' => (int) $gift->cost_points,
            ],
        );

        return back()->with('success', 'Cadeau cree.');
    }

    public function update(
        UpdateGiftConsoleRequest $request,
        int $giftId,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $gift = Gift::query()->findOrFail($giftId);
        $validated = $request->validated();
        $gift->fill([
                'slug' => $this->resolveGiftSlug($validated['title'], $validated['slug'] ?? $gift->slug, $gift->id),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
                'category' => $validated['category'] ?? null,
                'type' => $validated['type'] ?? null,
                'delivery_type' => $validated['delivery_type'] ?? null,
            'image_url' => $this->resolveImageUrl($request, $gift->image_url),
            'cost_points' => (int) $validated['cost_points'],
            'stock' => (int) $validated['stock'],
            'is_active' => $request->boolean('is_active', false),
            'is_featured' => $request->boolean('is_featured', false),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
                'requires_admin_validation' => $request->boolean('requires_admin_validation', false),
                'metadata' => $this->buildGiftMetadata($validated, $request, $gift),
        ])->save();

        $storeAuditLogAction->execute(
            action: 'gifts.updated',
            actor: $request->user(),
            target: $gift,
            context: [
                'gift_id' => $gift->id,
                'stock' => (int) $gift->stock,
                'cost_points' => (int) $gift->cost_points,
                'is_active' => (bool) $gift->is_active,
                'is_featured' => (bool) $gift->is_featured,
                'sort_order' => (int) $gift->sort_order,
            ],
        );

        return back()->with('success', 'Cadeau mis a jour.');
    }

    public function destroy(
        Request $request,
        int $giftId,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $gift = Gift::query()->findOrFail($giftId);

        if ($gift->redemptions()->exists()) {
            $gift->is_active = false;
            $gift->save();

            $storeAuditLogAction->execute(
                action: 'gifts.deactivated',
                actor: $request->user(),
                target: $gift,
                context: ['gift_id' => $gift->id],
            );

            return back()->with('success', 'Cadeau desactive (des redemptions existent deja).');
        }

        $storeAuditLogAction->execute(
            action: 'gifts.deleted',
            actor: $request->user(),
            target: $gift,
            context: ['gift_id' => $gift->id],
        );

        $gift->delete();

        return back()->with('success', 'Cadeau supprime.');
    }

    public function showRedemption(int $redemptionId): View
    {
        $redemption = GiftRedemption::query()
            ->whereKey($redemptionId)
            ->with([
                'user:id,name,email',
                'gift:id,title,cost_points,stock,is_active,image_url',
                'events' => fn ($query) => $query
                    ->with('actor:id,name,email')
                    ->orderByDesc('created_at'),
            ])
            ->firstOrFail();

        $walletTransactions = RewardWalletTransaction::query()
            ->where('user_id', $redemption->user_id)
            ->where(function ($query) use ($redemption): void {
                $query
                    ->where('ref_id', (string) $redemption->id)
                    ->orWhere('unique_key', 'like', '%redemption.'.$redemption->id.'%');
            })
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('pages.admin.gifts.show-redemption', [
            'redemption' => $redemption,
            'statusLabels' => GiftRedemption::statusLabels(),
            'walletTransactions' => $walletTransactions,
            'orderNumber' => 'CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT),
        ]);
    }


    public function approve(
        Request $request,
        int $redemptionId,
        StoreAuditLogAction $storeAuditLogAction,
        NotifyAction $notifyAction
    ): RedirectResponse {
        try {
            DB::transaction(function () use ($request, $redemptionId, $storeAuditLogAction, $notifyAction): void {
                $redemption = GiftRedemption::query()
                    ->whereKey($redemptionId)
                    ->with(['user', 'gift'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($redemption->status === GiftRedemption::STATUS_APPROVED) {
                    return;
                }

                if (in_array((string) $redemption->status, [
                    GiftRedemption::STATUS_REJECTED,
                    GiftRedemption::STATUS_CANCELLED,
                    GiftRedemption::STATUS_DELIVERED,
                    GiftRedemption::STATUS_REFUNDED,
                ], true)) {
                    throw new RuntimeException('Cette commande ne peut plus etre approuvee.');
                }

                $redemption->status = GiftRedemption::STATUS_APPROVED;
                $redemption->approved_at = $redemption->approved_at ?: now();
                $redemption->save();

                GiftRedemptionEvent::query()->create([
                    'redemption_id' => $redemption->id,
                    'actor_user_id' => $request->user()->id,
                    'type' => 'admin_approved',
                    'data' => ['status' => $redemption->status],
                    'created_at' => now(),
                ]);

                $storeAuditLogAction->execute(
                    action: 'gift.redeem.approve',
                    actor: $request->user(),
                    target: $redemption,
                    context: ['redemption_id' => $redemption->id],
                );

                $notifyAction->execute(
                    user: $redemption->user,
                    category: NotificationCategory::SYSTEM->value,
                    title: 'Cadeau approuve',
                    message: 'Ta demande de cadeau est approuvee.',
                    data: ['redemption_id' => $redemption->id, 'gift_id' => $redemption->gift_id],
                );
            });
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Redemption approuvee.');
    }

    public function reject(
        RejectGiftRedemptionRequest $request,
        int $redemptionId,
        ApplyRewardWalletTransactionAction $applyRewardWalletTransactionAction,
        StoreAuditLogAction $storeAuditLogAction,
        NotifyAction $notifyAction
    ): RedirectResponse {
        $reason = trim((string) $request->validated('reason'));

        try {
            DB::transaction(function () use (
                $request,
                $redemptionId,
                $reason,
                $applyRewardWalletTransactionAction,
                $storeAuditLogAction,
                $notifyAction
            ): void {
                $redemption = GiftRedemption::query()
                    ->whereKey($redemptionId)
                    ->with(['user', 'gift'])
                    ->lockForUpdate()
                    ->firstOrFail();
                $gift = Gift::query()->whereKey($redemption->gift_id)->lockForUpdate()->firstOrFail();

                if ($redemption->status === GiftRedemption::STATUS_REJECTED) {
                    return;
                }

                if (in_array((string) $redemption->status, [
                    GiftRedemption::STATUS_DELIVERED,
                    GiftRedemption::STATUS_REFUNDED,
                ], true)) {
                    throw new RuntimeException('Cette commande ne peut plus etre rejetee. Utilisez le remboursement si necessaire.');
                }

                $refundKey = 'gift.redeem.refund.redemption.'.$redemption->id;
                $refundExists = RewardWalletTransaction::query()
                    ->where('user_id', $redemption->user_id)
                    ->where('unique_key', $refundKey)
                    ->lockForUpdate()
                    ->exists();

                if (! $refundExists) {
                    $applyRewardWalletTransactionAction->execute(
                        user: $redemption->user,
                        type: RewardWalletTransaction::TYPE_REDEEM_REFUND,
                        amount: (int) $redemption->cost_points_snapshot,
                        uniqueKey: $refundKey,
                        refType: RewardWalletTransaction::REF_TYPE_GIFT,
                        refId: (string) $redemption->id,
                        metadata: ['reason' => $reason, 'actor_id' => $request->user()->id],
                    );

                    $gift->stock = (int) $gift->stock + 1;
                    $gift->save();
                }

                $redemption->status = GiftRedemption::STATUS_REJECTED;
                $redemption->reason = $reason;
                $redemption->rejected_at = $redemption->rejected_at ?: now();
                $redemption->save();

                GiftRedemptionEvent::query()->create([
                    'redemption_id' => $redemption->id,
                    'actor_user_id' => $request->user()->id,
                    'type' => 'admin_rejected',
                    'data' => ['reason' => $reason],
                    'created_at' => now(),
                ]);

                $storeAuditLogAction->execute(
                    action: 'gift.redeem.reject',
                    actor: $request->user(),
                    target: $redemption,
                    context: ['redemption_id' => $redemption->id, 'reason' => $reason],
                );

                $notifyAction->execute(
                    user: $redemption->user,
                    category: NotificationCategory::SYSTEM->value,
                    title: 'Cadeau rejete',
                    message: 'Ta demande de cadeau a ete rejetee.',
                    data: ['redemption_id' => $redemption->id, 'gift_id' => $redemption->gift_id, 'reason' => $reason],
                );
            });
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Redemption rejetee.');
    }

    public function ship(
        ShipGiftRedemptionRequest $request,
        int $redemptionId,
        StoreAuditLogAction $storeAuditLogAction,
        NotifyAction $notifyAction
    ): RedirectResponse {
        $validated = $request->validated();
        $trackingCode = $validated['tracking_code'] ?? null;
        $trackingCarrier = $validated['tracking_carrier'] ?? null;
        $shippingNote = $validated['shipping_note'] ?? null;

        try {
            DB::transaction(function () use (
                $request,
                $redemptionId,
                $trackingCode,
                $trackingCarrier,
                $shippingNote,
                $storeAuditLogAction,
                $notifyAction
            ): void {
                $redemption = GiftRedemption::query()
                    ->whereKey($redemptionId)
                    ->with(['user', 'gift'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($redemption->status === GiftRedemption::STATUS_SHIPPED) {
                    return;
                }

                if (in_array((string) $redemption->status, [
                    GiftRedemption::STATUS_REJECTED,
                    GiftRedemption::STATUS_CANCELLED,
                    GiftRedemption::STATUS_DELIVERED,
                    GiftRedemption::STATUS_REFUNDED,
                ], true)) {
                    throw new RuntimeException('Cette commande ne peut pas passer en expedition.');
                }

                $redemption->status = GiftRedemption::STATUS_SHIPPED;
                $redemption->tracking_code = $trackingCode ?: $redemption->tracking_code;
                $redemption->tracking_carrier = $trackingCarrier ?: $redemption->tracking_carrier;
                $redemption->shipping_note = $shippingNote ?: $redemption->shipping_note;
                $redemption->approved_at = $redemption->approved_at ?: now();
                $redemption->shipped_at = $redemption->shipped_at ?: now();
                $redemption->save();

                GiftRedemptionEvent::query()->create([
                    'redemption_id' => $redemption->id,
                    'actor_user_id' => $request->user()->id,
                    'type' => 'admin_shipped',
                    'data' => [
                        'tracking_code' => $redemption->tracking_code,
                        'tracking_carrier' => $redemption->tracking_carrier,
                        'shipping_note' => $redemption->shipping_note,
                    ],
                    'created_at' => now(),
                ]);

                $storeAuditLogAction->execute(
                    action: 'gift.redeem.ship',
                    actor: $request->user(),
                    target: $redemption,
                    context: [
                        'redemption_id' => $redemption->id,
                        'tracking_code' => $redemption->tracking_code,
                        'tracking_carrier' => $redemption->tracking_carrier,
                        'shipping_note' => $redemption->shipping_note,
                    ],
                );

                $notifyAction->execute(
                    user: $redemption->user,
                    category: NotificationCategory::SYSTEM->value,
                    title: 'Cadeau expedie',
                    message: 'Ta demande de cadeau est expediee.',
                    data: [
                        'redemption_id' => $redemption->id,
                        'gift_id' => $redemption->gift_id,
                        'tracking_code' => $redemption->tracking_code,
                        'tracking_carrier' => $redemption->tracking_carrier,
                    ],
                );
            });
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Redemption marquee comme expediee.');
    }

    public function deliver(
        Request $request,
        int $redemptionId,
        StoreAuditLogAction $storeAuditLogAction,
        NotifyAction $notifyAction
    ): RedirectResponse {
        try {
            DB::transaction(function () use ($request, $redemptionId, $storeAuditLogAction, $notifyAction): void {
                $redemption = GiftRedemption::query()
                    ->whereKey($redemptionId)
                    ->with(['user', 'gift'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($redemption->status === GiftRedemption::STATUS_DELIVERED) {
                    return;
                }

                if (in_array((string) $redemption->status, [
                    GiftRedemption::STATUS_REJECTED,
                    GiftRedemption::STATUS_CANCELLED,
                    GiftRedemption::STATUS_REFUNDED,
                ], true)) {
                    throw new RuntimeException('Cette commande ne peut pas passer en livree.');
                }

                $redemption->status = GiftRedemption::STATUS_DELIVERED;
                $redemption->approved_at = $redemption->approved_at ?: now();
                $redemption->shipped_at = $redemption->shipped_at ?: now();
                $redemption->delivered_at = $redemption->delivered_at ?: now();
                $redemption->save();

                GiftRedemptionEvent::query()->create([
                    'redemption_id' => $redemption->id,
                    'actor_user_id' => $request->user()->id,
                    'type' => 'admin_delivered',
                    'data' => ['status' => $redemption->status],
                    'created_at' => now(),
                ]);

                $storeAuditLogAction->execute(
                    action: 'gift.redeem.deliver',
                    actor: $request->user(),
                    target: $redemption,
                    context: ['redemption_id' => $redemption->id],
                );

                $notifyAction->execute(
                    user: $redemption->user,
                    category: NotificationCategory::SYSTEM->value,
                    title: 'Cadeau livre',
                    message: 'Ta demande de cadeau a ete livree.',
                    data: ['redemption_id' => $redemption->id, 'gift_id' => $redemption->gift_id],
                );
            });
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Redemption marquee comme livree.');
    }

    public function note(
        UpdateGiftRedemptionInternalNoteRequest $request,
        int $redemptionId,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $internalNote = trim((string) $request->validated('internal_note'));

        DB::transaction(function () use ($request, $redemptionId, $internalNote, $storeAuditLogAction): void {
            $redemption = GiftRedemption::query()
                ->whereKey($redemptionId)
                ->with(['user', 'gift'])
                ->lockForUpdate()
                ->firstOrFail();

            $redemption->internal_note = $internalNote;
            $redemption->save();

            GiftRedemptionEvent::query()->create([
                'redemption_id' => $redemption->id,
                'actor_user_id' => $request->user()->id,
                'type' => 'admin_note_added',
                'data' => ['internal_note' => $internalNote],
                'created_at' => now(),
            ]);

            $storeAuditLogAction->execute(
                action: 'gift.redeem.note',
                actor: $request->user(),
                target: $redemption,
                context: [
                    'redemption_id' => $redemption->id,
                    'internal_note' => $internalNote,
                ],
            );
        });

        return back()->with('success', 'Note interne enregistree.');
    }

    public function refund(
        RefundGiftRedemptionRequest $request,
        int $redemptionId,
        ApplyRewardWalletTransactionAction $applyRewardWalletTransactionAction,
        StoreAuditLogAction $storeAuditLogAction,
        NotifyAction $notifyAction
    ): RedirectResponse {
        $reason = trim((string) $request->validated('reason'));

        try {
            DB::transaction(function () use (
                $request,
                $redemptionId,
                $reason,
                $applyRewardWalletTransactionAction,
                $storeAuditLogAction,
                $notifyAction
            ): void {
                $redemption = GiftRedemption::query()
                    ->whereKey($redemptionId)
                    ->with(['user', 'gift'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($redemption->status === GiftRedemption::STATUS_REFUNDED) {
                    return;
                }

                $refundKey = 'gift.redeem.refund.redemption.'.$redemption->id;

                $applyRewardWalletTransactionAction->execute(
                    user: $redemption->user,
                    type: RewardWalletTransaction::TYPE_REDEEM_REFUND,
                    amount: (int) $redemption->cost_points_snapshot,
                    uniqueKey: $refundKey,
                    refType: RewardWalletTransaction::REF_TYPE_GIFT,
                    refId: (string) $redemption->id,
                    metadata: ['reason' => $reason, 'actor_id' => $request->user()->id, 'source' => 'admin_refund'],
                );

                $redemption->status = GiftRedemption::STATUS_REFUNDED;
                $redemption->reason = $redemption->reason ?: $reason;
                $redemption->save();

                GiftRedemptionEvent::query()->create([
                    'redemption_id' => $redemption->id,
                    'actor_user_id' => $request->user()->id,
                    'type' => 'admin_refunded',
                    'data' => ['reason' => $reason],
                    'created_at' => now(),
                ]);

                $storeAuditLogAction->execute(
                    action: 'gift.redeem.refund',
                    actor: $request->user(),
                    target: $redemption,
                    context: [
                        'redemption_id' => $redemption->id,
                        'reason' => $reason,
                    ],
                );

                $notifyAction->execute(
                    user: $redemption->user,
                    category: NotificationCategory::SYSTEM->value,
                    title: 'Points rembourses',
                    message: 'Les points de ta commande cadeau ont ete rembourses.',
                    data: [
                        'redemption_id' => $redemption->id,
                        'gift_id' => $redemption->gift_id,
                        'reason' => $reason,
                    ],
                );
            });
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Commande remboursee et journalisee.');
    }

    private function resolveImageUrl(Request $request, ?string $fallback = null): ?string
    {
        if ($request->hasFile('image_file')) {
            $path = MediaStorage::store($request->file('image_file'), 'gifts');

            return MediaStorage::url($path);
        }

        $imageUrl = trim((string) $request->input('image_url', ''));
        if ($imageUrl !== '') {
            return $imageUrl;
        }

        return $fallback;
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function buildGiftMetadata(array $validated, Request $request, ?Gift $gift = null): array
    {
        $metadata = is_array($gift?->metadata) ? $gift->metadata : [];

        $metadata['short_description'] = $this->nullableTrim($validated['short_description'] ?? null);
        $metadata['long_description'] = $this->nullableTrim($validated['long_description'] ?? null);
        $metadata['delivery_details'] = $this->nullableTrim($validated['delivery_details'] ?? null);
        $metadata['eligibility_details'] = $this->nullableTrim($validated['eligibility_details'] ?? null);
        $metadata['meta_title'] = $this->nullableTrim($validated['meta_title'] ?? null);
        $metadata['meta_description'] = $this->nullableTrim($validated['meta_description'] ?? null);
        $metadata['supporter_only'] = $request->boolean('supporter_only', false);
        $metadata['is_repeatable'] = $request->boolean('is_repeatable', true);
        $metadata['conditions'] = $this->normalizeTextList($validated['conditions'] ?? null);
        $metadata['gallery'] = $this->normalizeTextList($validated['gallery_urls'] ?? null);

        return collect($metadata)
            ->reject(function ($value, $key): bool {
                return in_array($key, ['short_description', 'long_description', 'delivery_details', 'eligibility_details', 'meta_title', 'meta_description'], true)
                    ? $value === null
                    : false;
            })
            ->all();
    }

    private function resolveGiftSlug(string $title, ?string $requestedSlug = null, ?int $ignoreGiftId = null): string
    {
        $baseSlug = Str::slug(trim((string) ($requestedSlug ?: $title)));
        $baseSlug = $baseSlug !== '' ? Str::limit($baseSlug, 150, '') : 'gift';
        $slug = $baseSlug;
        $suffix = 2;

        while (Gift::query()
            ->where('slug', $slug)
            ->when($ignoreGiftId !== null, fn ($query) => $query->whereKeyNot($ignoreGiftId))
            ->exists()) {
            $slug = Str::limit($baseSlug, 145, '').'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @return array<int, string>
     */
    private function normalizeTextList(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n|,/', trim((string) $value)) ?: [])
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    private function nullableTrim(?string $value): ?string
    {
        $trimmed = trim((string) $value);

        return $trimmed !== '' ? $trimmed : null;
    }
}
