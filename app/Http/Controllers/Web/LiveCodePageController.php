<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\RedeemLiveCodeRequest;
use App\Models\LiveCode;
use App\Services\LiveCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LiveCodePageController extends Controller
{
    public function index(): View
    {
        $codes = LiveCode::query()
            ->redeemable()
            ->with('missionTemplate:id,title,key')
            ->withCount('redemptions')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $myRedemptions = auth()->user()
            ? auth()->user()->liveCodeRedemptions()->with('liveCode')->latest('redeemed_at')->limit(10)->get()
            : collect();

        return view('pages.live-codes.index', [
            'codes' => $codes,
            'myRedemptions' => $myRedemptions,
        ]);
    }

    public function redeem(RedeemLiveCodeRequest $request, LiveCodeService $liveCodeService): RedirectResponse
    {
        try {
            $redemption = $liveCodeService->redeem($request->user(), $request->validated('code'));
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $redemption->load('liveCode.missionTemplate');

        $success = 'Code valide: +'.$redemption->xp_reward.' XP / +'.$redemption->reward_points.' points'
            .($redemption->bet_points > 0 ? ' / +'.$redemption->bet_points.' points paris' : '');

        if ($redemption->liveCode?->missionTemplate) {
            $success .= ' · mission associee prise en compte: '.$redemption->liveCode->missionTemplate->title;
        }

        return back()->with('success', $success);
    }
}
