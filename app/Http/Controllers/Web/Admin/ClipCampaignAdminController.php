<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clip;
use App\Models\ClipVoteCampaign;
use App\Models\ClipVoteEntry;
use App\Services\CreateClipVoteCampaign;
use App\Services\SettleClipVoteCampaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ClipCampaignAdminController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $campaigns = ClipVoteCampaign::query()
            ->withCount('votes')
            ->with(['entries.clip', 'winnerClip'])
            ->orderByDesc('starts_at')
            ->paginate(12)
            ->withQueryString();

        $clips = Clip::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(40)
            ->get();

        return view('pages.admin.clips.campaigns', [
            'campaigns' => $campaigns,
            'clips' => $clips,
            'statuses' => ClipVoteCampaign::statuses(),
            'types' => ClipVoteCampaign::types(),
        ]);
    }

    public function store(Request $request, CreateClipVoteCampaign $createClipVoteCampaign): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        try {
            $createClipVoteCampaign->execute($validated);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Campagne clips creee.');
    }

    public function update(Request $request, int $campaignId): RedirectResponse
    {
        $campaign = ClipVoteCampaign::query()->with('entries')->findOrFail($campaignId);
        $validated = $this->validatePayload($request, false);

        $clipIds = collect($validated['clip_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values();

        if ($clipIds->isNotEmpty()) {
            $publishedCount = Clip::query()->published()->whereIn('id', $clipIds)->count();
            if ($publishedCount !== $clipIds->count()) {
                return back()->with('error', 'Tous les clips d une campagne doivent etre publies.');
            }
        }

        DB::transaction(function () use ($campaign, $validated, $clipIds): void {
            $campaign->fill([
                'type' => $validated['type'] ?? $campaign->type,
                'title' => $validated['title'] ?? $campaign->title,
                'starts_at' => $validated['starts_at'] ?? $campaign->starts_at,
                'ends_at' => $validated['ends_at'] ?? $campaign->ends_at,
                'status' => $validated['status'] ?? $campaign->status,
            ])->save();

            if ($clipIds->isNotEmpty()) {
                ClipVoteEntry::query()->where('campaign_id', $campaign->id)->whereNotIn('clip_id', $clipIds)->delete();
                foreach ($clipIds as $clipId) {
                    ClipVoteEntry::query()->firstOrCreate([
                        'campaign_id' => $campaign->id,
                        'clip_id' => $clipId,
                    ]);
                }
            }
        });

        return back()->with('success', 'Campagne clips mise a jour.');
    }

    public function close(int $campaignId): RedirectResponse
    {
        $campaign = ClipVoteCampaign::query()->findOrFail($campaignId);
        $campaign->status = ClipVoteCampaign::STATUS_CLOSED;
        $campaign->save();

        return back()->with('success', 'Campagne clips cloturee.');
    }

    public function settle(int $campaignId, SettleClipVoteCampaign $settleClipVoteCampaign): RedirectResponse
    {
        $campaign = ClipVoteCampaign::query()->findOrFail($campaignId);
        $settleClipVoteCampaign->execute($campaign);

        return back()->with('success', 'Campagne clips soldee.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, bool $clipIdsRequired = true): array
    {
        return $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', ClipVoteCampaign::types())],
            'title' => ['required', 'string', 'max:191'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['nullable', 'string', 'in:'.implode(',', ClipVoteCampaign::statuses())],
            'clip_ids' => [$clipIdsRequired ? 'required' : 'nullable', 'array', 'min:1'],
            'clip_ids.*' => ['integer'],
        ]);
    }
}
