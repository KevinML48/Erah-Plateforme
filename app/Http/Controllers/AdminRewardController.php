<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRewardRequest;
use App\Http\Requests\UpdateRewardRequest;
use App\Models\AdminAuditLog;
use App\Models\Reward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRewardController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
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

    public function store(StoreRewardRequest $request): JsonResponse|RedirectResponse
    {
        $reward = Reward::query()->create(array_merge(
            $request->validated(),
            ['created_by' => $request->user()?->id]
        ));

        AdminAuditLog::query()->create([
            'actor_user_id' => $request->user()?->id,
            'action' => 'reward.create',
            'entity_type' => 'reward',
            'entity_id' => (int) $reward->id,
            'payload_json' => $reward->toArray(),
        ]);

        if (!$request->expectsJson()) {
            return redirect()->route('admin.rewards.index')->with('status', 'Reward created.');
        }

        return response()->json([
            'message' => 'Reward created.',
            'reward' => $reward,
        ], 201);
    }

    public function update(UpdateRewardRequest $request, Reward $reward): JsonResponse|RedirectResponse
    {
        $reward->fill($request->validated());
        $reward->save();

        AdminAuditLog::query()->create([
            'actor_user_id' => $request->user()?->id,
            'action' => 'reward.update',
            'entity_type' => 'reward',
            'entity_id' => (int) $reward->id,
            'payload_json' => $request->validated(),
        ]);

        if (!$request->expectsJson()) {
            return redirect()->route('admin.rewards.index')->with('status', 'Reward updated.');
        }

        return response()->json([
            'message' => 'Reward updated.',
            'reward' => $reward,
        ]);
    }

    public function destroy(Request $request, Reward $reward): JsonResponse|RedirectResponse
    {
        $reward->delete();

        AdminAuditLog::query()->create([
            'actor_user_id' => $request->user()?->id,
            'action' => 'reward.delete',
            'entity_type' => 'reward',
            'entity_id' => (int) $reward->id,
            'payload_json' => null,
        ]);

        if (!$request->expectsJson()) {
            return redirect()->route('admin.rewards.index')->with('status', 'Reward deleted.');
        }

        return response()->json(['message' => 'Reward deleted.']);
    }
}
