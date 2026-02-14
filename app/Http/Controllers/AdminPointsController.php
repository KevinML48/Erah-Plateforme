<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PointTransactionType;
use App\Exceptions\InsufficientPointsException;
use App\Http\Requests\AdminAdjustPointsRequest;
use App\Models\PointLog;
use App\Models\User;
use App\Services\AdminAuditService;
use App\Services\PointService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminPointsController extends Controller
{
    public function index(): View
    {
        $this->authorize('manage-points');

        return view('pages.admin.points-adjustment', [
            'title' => 'Admin points',
        ]);
    }

    public function store(
        AdminAdjustPointsRequest $request,
        PointService $pointService,
        AdminAuditService $auditService
    ): RedirectResponse {
        $this->authorize('manage-points');

        $validated = $request->validated();
        $targetUser = User::query()->where('email', $validated['email'])->firstOrFail();
        $amount = (int) $validated['amount'];
        $reason = (string) $validated['reason'];
        $idempotencyKey = 'admin-adjustment:'.$targetUser->id.':'.Str::uuid()->toString();

        try {
            if ($amount > 0) {
                $pointService->addPoints(
                    user: $targetUser,
                    amount: $amount,
                    type: PointTransactionType::AdminAdjustment->value,
                    description: $reason,
                    referenceId: (int) auth()->id(),
                    referenceType: 'admin',
                    idempotencyKey: $idempotencyKey
                );
            } else {
                $pointService->removePoints(
                    user: $targetUser,
                    amount: abs($amount),
                    type: PointTransactionType::AdminAdjustment->value,
                    description: $reason,
                    referenceId: (int) auth()->id(),
                    referenceType: 'admin',
                    idempotencyKey: $idempotencyKey
                );
            }
        } catch (InsufficientPointsException $exception) {
            return back()->withErrors([
                'amount' => $exception->getMessage(),
            ])->withInput();
        }

        Log::channel('daily')->info('admin.points_adjustment', [
            'actor_user_id' => auth()->id(),
            'target_user_id' => $targetUser->id,
            'amount' => $amount,
            'reason' => $reason,
            'idempotency_key' => $idempotencyKey,
        ]);

        $auditService->log(
            actor: $request->user(),
            action: 'points.adjust',
            entityType: 'user',
            entityId: (int) $targetUser->id,
            metadata: [
                'amount' => $amount,
                'reason' => $reason,
                'idempotency_key' => $idempotencyKey,
            ]
        );

        return redirect()
            ->route('admin.points.index')
            ->with('status', 'Ajustement applique avec succes.');
    }

    public function metrics(): View
    {
        $this->authorize('manage-points');

        $dailyCredits = (int) PointLog::query()
            ->whereDate('created_at', now()->toDateString())
            ->where('amount', '>', 0)
            ->sum('amount');

        $dailyDebits = (int) abs((int) PointLog::query()
            ->whereDate('created_at', now()->toDateString())
            ->where('amount', '<', 0)
            ->sum('amount'));

        $weeklyTransactions = (int) PointLog::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $anomalyCount = (int) PointLog::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->whereRaw('ABS(amount) >= 10000')
            ->count();

        return view('pages.admin.points-metrics', [
            'title' => 'Points Metrics',
            'dailyCredits' => $dailyCredits,
            'dailyDebits' => $dailyDebits,
            'weeklyTransactions' => $weeklyTransactions,
            'anomalyCount' => $anomalyCount,
        ]);
    }
}
