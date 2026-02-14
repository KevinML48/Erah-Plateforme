<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRewardRequest;
use App\Http\Requests\UpdateRewardRequest;
use App\Models\Reward;
use App\Services\AdminAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRewardController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        $this->authorize('manage-rewards');

        $rewards = Reward::query()
            ->when($request->filled('is_active'), function ($query) use ($request): void {
                $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOL));
            })
            ->orderByDesc('id')
            ->paginate(30);

        if (!$request->expectsJson()) {
            return view('pages.admin.rewards.index', [
                'title' => 'Admin Rewards',
                'rewards' => $rewards,
            ]);
        }

        return response()->json($rewards);
    }

    public function store(StoreRewardRequest $request, AdminAuditService $auditService): JsonResponse|RedirectResponse
    {
        $this->authorize('manage-rewards');

        $reward = Reward::query()->create(array_merge(
            $request->validated(),
            ['created_by' => $request->user()?->id]
        ));

        $auditService->log(
            actor: $request->user(),
            action: 'reward.create',
            entityType: 'reward',
            entityId: (int) $reward->id,
            metadata: ['after' => $reward->toArray()]
        );

        if (!$request->expectsJson()) {
            return redirect()->route('admin.rewards.index')->with('status', 'Reward created.');
        }

        return response()->json([
            'message' => 'Reward created.',
            'reward' => $reward,
        ], 201);
    }

    public function update(
        UpdateRewardRequest $request,
        Reward $reward,
        AdminAuditService $auditService
    ): JsonResponse|RedirectResponse {
        $this->authorize('manage-rewards');

        $before = $reward->only([
            'name',
            'slug',
            'description',
            'points_cost',
            'stock',
            'is_active',
            'image_url',
            'starts_at',
            'ends_at',
        ]);

        $reward->fill($request->validated());
        $reward->save();

        $auditService->log(
            actor: $request->user(),
            action: 'reward.update',
            entityType: 'reward',
            entityId: (int) $reward->id,
            metadata: [
                'before' => $before,
                'after' => $reward->only(array_keys($before)),
            ]
        );

        if (!$request->expectsJson()) {
            return redirect()->route('admin.rewards.index')->with('status', 'Reward updated.');
        }

        return response()->json([
            'message' => 'Reward updated.',
            'reward' => $reward,
        ]);
    }

    public function destroy(
        Request $request,
        Reward $reward,
        AdminAuditService $auditService
    ): JsonResponse|RedirectResponse {
        $this->authorize('manage-rewards');

        $snapshot = $reward->toArray();
        $reward->delete();

        $auditService->log(
            actor: $request->user(),
            action: 'reward.delete',
            entityType: 'reward',
            entityId: (int) $reward->id,
            metadata: ['before' => $snapshot]
        );

        if (!$request->expectsJson()) {
            return redirect()->route('admin.rewards.index')->with('status', 'Reward deleted.');
        }

        return response()->json(['message' => 'Reward deleted.']);
    }
}
