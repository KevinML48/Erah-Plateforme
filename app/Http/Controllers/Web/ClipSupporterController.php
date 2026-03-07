<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Clip;
use App\Models\ClipSupporterReaction;
use App\Models\ClipVote;
use App\Models\ClipVoteCampaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClipSupporterController extends Controller
{
    /**
     * @return array<int, array{key: string, label: string, icon: string}>
     */
    public static function reactionOptions(): array
    {
        return [
            ['key' => 'fire', 'label' => 'Fire', 'icon' => 'fa-solid fa-fire'],
            ['key' => 'ice', 'label' => 'Ice', 'icon' => 'fa-regular fa-snowflake'],
            ['key' => 'crown', 'label' => 'Crown', 'icon' => 'fa-solid fa-crown'],
            ['key' => 'diamond', 'label' => 'Diamond', 'icon' => 'fa-regular fa-gem'],
        ];
    }

    public function storeReaction(Request $request, int $clipId): RedirectResponse
    {
        $validated = $request->validate([
            'reaction_key' => ['required', 'string', 'in:'.implode(',', array_column(self::reactionOptions(), 'key'))],
        ]);

        $clip = Clip::query()->published()->whereKey($clipId)->firstOrFail();

        ClipSupporterReaction::query()->firstOrCreate([
            'clip_id' => $clip->id,
            'user_id' => $request->user()->id,
            'reaction_key' => $validated['reaction_key'],
        ]);

        return back()->with('success', 'Reaction supporter ajoutee.');
    }

    public function destroyReaction(Request $request, int $clipId, string $reactionKey): RedirectResponse
    {
        $clip = Clip::query()->published()->whereKey($clipId)->firstOrFail();

        ClipSupporterReaction::query()
            ->where('clip_id', $clip->id)
            ->where('user_id', $request->user()->id)
            ->where('reaction_key', $reactionKey)
            ->delete();

        return back()->with('success', 'Reaction supporter retiree.');
    }

    public function vote(Request $request, int $campaignId): RedirectResponse
    {
        $validated = $request->validate([
            'clip_id' => ['required', 'integer'],
        ]);

        $campaign = ClipVoteCampaign::query()
            ->active()
            ->with('entries')
            ->whereKey($campaignId)
            ->firstOrFail();

        abort_unless(
            $campaign->entries->contains(fn ($entry) => (int) $entry->clip_id === (int) $validated['clip_id']),
            422,
            'Clip invalide pour cette campagne.'
        );

        ClipVote::query()->updateOrCreate([
            'campaign_id' => $campaign->id,
            'user_id' => $request->user()->id,
        ], [
            'clip_id' => (int) $validated['clip_id'],
        ]);

        return back()->with('success', 'Vote supporter enregistre.');
    }
}
