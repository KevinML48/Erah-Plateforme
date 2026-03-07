<?php

namespace App\Services;

use App\Models\ClipVote;
use App\Models\ClipVoteCampaign;
use Illuminate\Support\Facades\DB;

class SettleClipVoteCampaign
{
    public function execute(ClipVoteCampaign $campaign): ClipVoteCampaign
    {
        return DB::transaction(function () use ($campaign): ClipVoteCampaign {
            $lockedCampaign = ClipVoteCampaign::query()
                ->whereKey($campaign->id)
                ->lockForUpdate()
                ->firstOrFail();

            $winnerClipId = ClipVote::query()
                ->where('campaign_id', $lockedCampaign->id)
                ->selectRaw('clip_id, COUNT(*) as votes_count')
                ->groupBy('clip_id')
                ->orderByDesc('votes_count')
                ->orderBy('clip_id')
                ->value('clip_id');

            $lockedCampaign->winner_clip_id = $winnerClipId ? (int) $winnerClipId : null;
            $lockedCampaign->status = ClipVoteCampaign::STATUS_SETTLED;
            $lockedCampaign->save();

            return $lockedCampaign->fresh(['winnerClip', 'entries.clip']);
        });
    }
}
