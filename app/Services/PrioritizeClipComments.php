<?php

namespace App\Services;

use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\UserSupportSubscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PrioritizeClipComments
{
    public function execute(Clip $clip, int $perPage = 10, ?int $page = null): LengthAwarePaginator
    {
        $activeSupporters = UserSupportSubscription::query()
            ->active()
            ->selectRaw('DISTINCT user_id, 1 as supporter_priority');

        return ClipComment::query()
            ->where('clip_comments.clip_id', $clip->id)
            ->whereNull('clip_comments.parent_id')
            ->where('clip_comments.status', ClipComment::STATUS_PUBLISHED)
            ->leftJoinSub($activeSupporters, 'supporter_flags', function ($join): void {
                $join->on('supporter_flags.user_id', '=', 'clip_comments.user_id');
            })
            ->select([
                'clip_comments.*',
                DB::raw('COALESCE(supporter_flags.supporter_priority, 0) as supporter_priority'),
            ])
            ->with([
                'user:id,name,avatar_path,provider_avatar_url',
                'replies' => fn ($query) => $query
                    ->where('status', ClipComment::STATUS_PUBLISHED)
                    ->with('user:id,name,avatar_path,provider_avatar_url')
                    ->orderBy('id'),
            ])
            ->orderByDesc('supporter_priority')
            ->orderByDesc('clip_comments.id')
            ->paginate($perPage, ['*'], 'comments_page', $page);
    }
}
