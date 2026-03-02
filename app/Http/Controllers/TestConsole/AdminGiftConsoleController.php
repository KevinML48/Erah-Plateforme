<?php

namespace App\Http\Controllers\TestConsole;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Application\Actions\Rewards\ApplyRewardWalletTransactionAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Console\StoreGiftConsoleRequest;
use App\Http\Requests\Web\Console\UpdateGiftConsoleRequest;
use App\Http\Requests\Web\Console\UpdateGiftRedemptionRequest;
use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\GiftRedemptionEvent;
use App\Models\RewardWalletTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminGiftConsoleController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'all');

        $gifts = Gift::query()
            ->orderBy('id')
            ->paginate(20, ['*'], 'gifts_page')
            ->withQueryString();

        $redemptions = GiftRedemption::query()
            ->with(['user:id,name,email', 'gift:id,title'])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->orderByDesc('requested_at')
            ->paginate(20, ['*'], 'redemptions_page')
            ->withQueryString();

        return view('pages.admin.gifts.index', [
            'gifts' => $gifts,
            'redemptions' => $redemptions,
            'status' => $status,
            'statuses' => GiftRedemption::statuses(),
        ]);
    }

    public function store(StoreGiftConsoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        Gift::query()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'cost_points' => (int) $validated['cost_points'],
            'stock' => (int) $validated['stock'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Cadeau cree.');
    }

    public function update(UpdateGiftConsoleRequest $request, int $giftId): RedirectResponse
    {
        $gift = Gift::query()->findOrFail($giftId);
        $validated = $request->validated();
        $gift->fill([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'cost_points' => (int) $validated['cost_points'],
            'stock' => (int) $validated['stock'],
            'is_active' => $request->boolean('is_active', false),
        ])->save();

        return back()->with('success', 'Cadeau mis a jour.');
    }

    public function destroy(int $giftId): RedirectResponse
    {
        $gift = Gift::query()->findOrFail($giftId);

        if ($gift->redemptions()->exists()) {
            $gift->is_active = false;
            $gift->save();

            return back()->with('success', 'Cadeau desactive (des redemptions existent deja).');
        }

        $gift->delete();

        return back()->with('success', 'Cadeau supprime.');
    }

    public function approve(
        Request $request,
        int $redemptionId,
        StoreAuditLogAction $storeAuditLogAction,
        NotifyAction $notifyAction
    ): RedirectResponse {
        DB::transaction(function () use ($request, $redemptionId, $storeAuditLogAction, $notifyAction): void {
            $redemption = GiftRedemption::query()->whereKey($redemptionId)->lockForUpdate()->firstOrFail();

            if ($redemption->status !== GiftRedemption::STATUS_APPROVED) {
                $redemption->status = GiftRedemption::STATUS_APPROVED;
                $redemption->approved_at = $redemption->approved_at ?: now();
                $redemption->save();
            }

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

        return back()->with('success', 'Redemption approuvee.');
    }

    public function reject(
        UpdateGiftRedemptionRequest $request,
        int $redemptionId,
        ApplyRewardWalletTransactionAction $applyRewardWalletTransactionAction,
        StoreAuditLogAction $storeAuditLogAction,
        NotifyAction $notifyAction
    ): RedirectResponse {
        $reason = (string) ($request->validated()['reason'] ?? 'rejected_by_admin');

        DB::transaction(function () use (
            $request,
            $redemptionId,
            $reason,
            $applyRewardWalletTransactionAction,
            $storeAuditLogAction,
            $notifyAction
        ): void {
            $redemption = GiftRedemption::query()->whereKey($redemptionId)->lockForUpdate()->firstOrFail();
            $gift = Gift::query()->whereKey($redemption->gift_id)->lockForUpdate()->firstOrFail();

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

        return back()->with('success', 'Redemption rejetee.');
    }

    public function ship(
        UpdateGiftRedemptionRequest $request,
        int $redemptionId,
        StoreAuditLogAction $storeAuditLogAction,
        NotifyAction $notifyAction
    ): RedirectResponse {
        $trackingCode = $request->validated()['tracking_code'] ?? null;

        DB::transaction(function () use ($request, $redemptionId, $trackingCode, $storeAuditLogAction, $notifyAction): void {
            $redemption = GiftRedemption::query()->whereKey($redemptionId)->lockForUpdate()->firstOrFail();

            if ($redemption->status !== GiftRedemption::STATUS_SHIPPED) {
                $redemption->status = GiftRedemption::STATUS_SHIPPED;
                $redemption->tracking_code = $trackingCode ?: $redemption->tracking_code;
                $redemption->shipped_at = $redemption->shipped_at ?: now();
                $redemption->save();
            }

            GiftRedemptionEvent::query()->create([
                'redemption_id' => $redemption->id,
                'actor_user_id' => $request->user()->id,
                'type' => 'admin_shipped',
                'data' => ['tracking_code' => $redemption->tracking_code],
                'created_at' => now(),
            ]);

            $storeAuditLogAction->execute(
                action: 'gift.redeem.ship',
                actor: $request->user(),
                target: $redemption,
                context: ['redemption_id' => $redemption->id, 'tracking_code' => $redemption->tracking_code],
            );

            $notifyAction->execute(
                user: $redemption->user,
                category: NotificationCategory::SYSTEM->value,
                title: 'Cadeau expedie',
                message: 'Ta demande de cadeau est expediee.',
                data: ['redemption_id' => $redemption->id, 'gift_id' => $redemption->gift_id, 'tracking_code' => $redemption->tracking_code],
            );
        });

        return back()->with('success', 'Redemption marquee comme expediee.');
    }

    public function deliver(
        Request $request,
        int $redemptionId,
        StoreAuditLogAction $storeAuditLogAction,
        NotifyAction $notifyAction
    ): RedirectResponse {
        DB::transaction(function () use ($request, $redemptionId, $storeAuditLogAction, $notifyAction): void {
            $redemption = GiftRedemption::query()->whereKey($redemptionId)->lockForUpdate()->firstOrFail();

            if ($redemption->status !== GiftRedemption::STATUS_DELIVERED) {
                $redemption->status = GiftRedemption::STATUS_DELIVERED;
                $redemption->delivered_at = $redemption->delivered_at ?: now();
                $redemption->save();
            }

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

        return back()->with('success', 'Redemption marquee comme livree.');
    }
}
