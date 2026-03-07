<?php

namespace App\Services;

use App\Models\Clip;
use App\Models\ClipVoteCampaign;
use App\Models\ClipVoteEntry;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateClipVoteCampaign
{
    public function execute(array $payload): ClipVoteCampaign
    {
        $clipIds = collect($payload['clip_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values();

        if ($clipIds->isEmpty()) {
            throw new RuntimeException('Au moins un clip doit etre selectionne pour la campagne.');
        }

        $validClipIds = Clip::query()
            ->published()
            ->whereIn('id', $clipIds)
            ->pluck('id');

        if ($validClipIds->count() !== $clipIds->count()) {
            throw new RuntimeException('Tous les clips de campagne doivent etre publies.');
        }

        return DB::transaction(function () use ($payload, $validClipIds): ClipVoteCampaign {
            $campaign = ClipVoteCampaign::query()->create([
                'type' => (string) $payload['type'],
                'title' => (string) $payload['title'],
                'starts_at' => $payload['starts_at'],
                'ends_at' => $payload['ends_at'],
                'status' => (string) ($payload['status'] ?? ClipVoteCampaign::STATUS_DRAFT),
            ]);

            foreach ($validClipIds as $clipId) {
                ClipVoteEntry::query()->create([
                    'campaign_id' => $campaign->id,
                    'clip_id' => (int) $clipId,
                ]);
            }

            return $campaign->fresh(['entries.clip']);
        });
    }
}
