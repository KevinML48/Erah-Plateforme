<?php

namespace App\Http\Controllers\Web\Admin;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\UpsertLiveCodeRequest;
use App\Models\LiveCode;
use App\Services\LiveCodeService;
use Illuminate\Http\RedirectResponse;

class AdminLiveCodeController extends Controller
{
    public function store(UpsertLiveCodeRequest $request, LiveCodeService $liveCodeService): RedirectResponse
    {
        $liveCodeService->generate($request->validated(), $request->user());

        return back()->with('success', 'Code live cree.');
    }

    public function update(
        UpsertLiveCodeRequest $request,
        int $liveCodeId,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $liveCode = LiveCode::query()->findOrFail($liveCodeId);
        $liveCode->fill([
            'code' => $request->validated('code') ?: $liveCode->code,
            'label' => $request->validated('label'),
            'description' => $request->validated('description'),
            'status' => $request->validated('status'),
            'reward_points' => (int) ($request->validated('reward_points') ?? 0),
            'bet_points' => (int) ($request->validated('bet_points') ?? 0),
            'xp_reward' => (int) ($request->validated('xp_reward') ?? 0),
            'usage_limit' => $request->validated('usage_limit'),
            'per_user_limit' => (int) ($request->validated('per_user_limit') ?? 1),
            'expires_at' => $request->validated('expires_at'),
            'mission_template_id' => $request->validated('mission_template_id'),
        ])->save();

        $storeAuditLogAction->execute(
            action: 'live-codes.updated',
            actor: $request->user(),
            target: $liveCode,
            context: [
                'live_code_id' => $liveCode->id,
                'status' => $liveCode->status,
            ],
        );

        return back()->with('success', 'Code live mis a jour.');
    }

    public function destroy(int $liveCodeId, StoreAuditLogAction $storeAuditLogAction): RedirectResponse
    {
        $liveCode = LiveCode::query()->findOrFail($liveCodeId);
        $storeAuditLogAction->execute(
            action: 'live-codes.deleted',
            actor: request()->user(),
            target: $liveCode,
            context: [
                'live_code_id' => $liveCode->id,
                'status' => $liveCode->status,
            ],
        );
        $liveCode->delete();

        return back()->with('success', 'Code live supprime.');
    }
}
