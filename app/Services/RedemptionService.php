<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\PointTransactionType;
use App\Enums\RewardRedemptionStatus;
use App\Exceptions\OutOfStockException;
use App\Exceptions\RedemptionAlreadyProcessedException;
use App\Exceptions\RedemptionNotAllowedException;
use App\Exceptions\RewardNotAvailableException;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RedemptionService
{
    public function __construct(
        private readonly PointService $pointService,
        private readonly AdminAuditService $adminAuditService
    ) {
    }

    public function createRedemption(User $user, Reward $reward, array $shippingData = []): RewardRedemption
    {
        return DB::transaction(function () use ($user, $reward, $shippingData): RewardRedemption {
            /** @var Reward $lockedReward */
            $lockedReward = Reward::query()->whereKey($reward->id)->lockForUpdate()->firstOrFail();

            if (!$lockedReward->is_active) {
                throw new RewardNotAvailableException();
            }

            if ($lockedReward->starts_at !== null && now()->lt($lockedReward->starts_at)) {
                throw new RewardNotAvailableException();
            }

            if ($lockedReward->ends_at !== null && now()->gt($lockedReward->ends_at)) {
                throw new RewardNotAvailableException();
            }

            if ($lockedReward->stock !== null && $lockedReward->stock <= 0) {
                throw new OutOfStockException();
            }

            $redemption = RewardRedemption::query()->create(array_merge(
                [
                    'reward_id' => $lockedReward->id,
                    'user_id' => $user->id,
                    'status' => RewardRedemptionStatus::Pending,
                    'points_cost_snapshot' => (int) $lockedReward->points_cost,
                    'reward_name_snapshot' => (string) $lockedReward->name,
                ],
                Arr::only($shippingData, [
                    'shipping_name',
                    'shipping_email',
                    'shipping_phone',
                    'shipping_address1',
                    'shipping_address2',
                    'shipping_city',
                    'shipping_postal_code',
                    'shipping_country',
                ])
            ));

            $this->pointService->removePoints(
                user: $user,
                amount: (int) $redemption->points_cost_snapshot,
                type: PointTransactionType::RewardRedeem->value,
                description: 'Redemption #'.$redemption->id,
                referenceId: (int) $redemption->id,
                referenceType: 'redemption',
                idempotencyKey: 'redemption-debit:'.$redemption->id
            );

            $redemption->debited_points = true;

            if ($lockedReward->stock !== null) {
                if ($lockedReward->stock <= 0) {
                    throw new OutOfStockException();
                }

                $lockedReward->stock -= 1;
                $lockedReward->save();
                $redemption->reserved_stock = true;
            }

            $redemption->save();

            return $redemption->refresh();
        });
    }

    public function cancelRedemption(User $user, RewardRedemption $redemption): RewardRedemption
    {
        return DB::transaction(function () use ($user, $redemption): RewardRedemption {
            /** @var RewardRedemption $lockedRedemption */
            $lockedRedemption = RewardRedemption::query()->whereKey($redemption->id)->lockForUpdate()->firstOrFail();

            if ((int) $lockedRedemption->user_id !== (int) $user->id) {
                throw new RedemptionNotAllowedException();
            }

            if ($lockedRedemption->status === RewardRedemptionStatus::Cancelled && $lockedRedemption->refunded_points) {
                return $lockedRedemption;
            }

            if (!$lockedRedemption->canCancel()) {
                throw new RedemptionNotAllowedException('Impossible d annuler cette demande.');
            }

            return $this->processRefundAndStockRestore(
                actor: $user,
                redemption: $lockedRedemption,
                nextStatus: RewardRedemptionStatus::Cancelled,
                note: null,
                action: 'redemption.cancel'
            );
        });
    }

    public function approveRedemption(User $admin, RewardRedemption $redemption): RewardRedemption
    {
        return DB::transaction(function () use ($admin, $redemption): RewardRedemption {
            /** @var RewardRedemption $lockedRedemption */
            $lockedRedemption = RewardRedemption::query()->whereKey($redemption->id)->lockForUpdate()->firstOrFail();

            if (in_array($lockedRedemption->status, [RewardRedemptionStatus::Approved, RewardRedemptionStatus::Shipped], true)) {
                return $lockedRedemption;
            }

            if (!$lockedRedemption->canApprove()) {
                throw new RedemptionNotAllowedException('Cette demande ne peut pas etre approuvee.');
            }

            $lockedRedemption->status = RewardRedemptionStatus::Approved;
            $lockedRedemption->approved_at = now();
            $lockedRedemption->save();

            $this->logAdminAction($admin, 'redemption.approve', 'reward_redemption', (int) $lockedRedemption->id, [
                'status' => $lockedRedemption->status->value,
            ]);

            return $lockedRedemption->refresh();
        });
    }

    public function rejectRedemption(User $admin, RewardRedemption $redemption, ?string $note = null): RewardRedemption
    {
        return DB::transaction(function () use ($admin, $redemption, $note): RewardRedemption {
            /** @var RewardRedemption $lockedRedemption */
            $lockedRedemption = RewardRedemption::query()->whereKey($redemption->id)->lockForUpdate()->firstOrFail();

            if ($lockedRedemption->status === RewardRedemptionStatus::Rejected && $lockedRedemption->refunded_points) {
                return $lockedRedemption;
            }

            if ($lockedRedemption->status !== RewardRedemptionStatus::Pending) {
                throw new RedemptionNotAllowedException('Seules les demandes PENDING peuvent etre refusees.');
            }

            return $this->processRefundAndStockRestore(
                actor: $admin,
                redemption: $lockedRedemption,
                nextStatus: RewardRedemptionStatus::Rejected,
                note: $note,
                action: 'redemption.reject'
            );
        });
    }

    public function markShipped(User $admin, RewardRedemption $redemption, ?string $tracking = null): RewardRedemption
    {
        return DB::transaction(function () use ($admin, $redemption, $tracking): RewardRedemption {
            /** @var RewardRedemption $lockedRedemption */
            $lockedRedemption = RewardRedemption::query()->whereKey($redemption->id)->lockForUpdate()->firstOrFail();

            if ($lockedRedemption->status === RewardRedemptionStatus::Shipped) {
                return $lockedRedemption;
            }

            if (!$lockedRedemption->canShip()) {
                throw new RedemptionNotAllowedException('La demande doit etre APPROVED pour etre expediee.');
            }

            $lockedRedemption->status = RewardRedemptionStatus::Shipped;
            $lockedRedemption->shipped_at = now();
            $lockedRedemption->tracking_code = $tracking;
            $lockedRedemption->save();

            $this->logAdminAction($admin, 'redemption.ship', 'reward_redemption', (int) $lockedRedemption->id, [
                'tracking_code' => $tracking,
            ]);

            return $lockedRedemption->refresh();
        });
    }

    private function processRefundAndStockRestore(
        User $actor,
        RewardRedemption $redemption,
        RewardRedemptionStatus $nextStatus,
        ?string $note,
        string $action
    ): RewardRedemption {
        if (!$redemption->debited_points) {
            throw new RedemptionAlreadyProcessedException('Aucun debit detecte pour cette demande.');
        }

        if (!$redemption->refunded_points) {
            $this->pointService->addPoints(
                user: $redemption->user()->lockForUpdate()->firstOrFail(),
                amount: (int) $redemption->points_cost_snapshot,
                type: PointTransactionType::RewardRefund->value,
                description: 'Refund redemption #'.$redemption->id,
                referenceId: (int) $redemption->id,
                referenceType: 'redemption',
                idempotencyKey: 'redemption-refund:'.$redemption->id
            );

            $redemption->refunded_points = true;

            if ($redemption->reserved_stock) {
                $lockedReward = Reward::query()->whereKey($redemption->reward_id)->lockForUpdate()->firstOrFail();
                if ($lockedReward->stock !== null) {
                    $lockedReward->stock += 1;
                    $lockedReward->save();
                }
            }
        }

        $redemption->status = $nextStatus;
        $redemption->admin_note = $note;
        if ($nextStatus === RewardRedemptionStatus::Cancelled) {
            $redemption->cancelled_at = now();
        }
        $redemption->save();

        $this->logAdminAction($actor, $action, 'reward_redemption', (int) $redemption->id, [
            'status' => $redemption->status->value,
            'refunded_points' => (bool) $redemption->refunded_points,
        ]);

        return $redemption->refresh();
    }

    private function logAdminAction(User $actor, string $action, string $entityType, int $entityId, array $payload): void
    {
        $this->adminAuditService->log(
            actor: $actor,
            action: $action,
            entityType: $entityType,
            entityId: $entityId,
            metadata: $payload
        );
    }
}
