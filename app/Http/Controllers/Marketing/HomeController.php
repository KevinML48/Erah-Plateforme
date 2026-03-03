<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Clip;
use App\Models\EsportMatch;
use App\Models\UserProgress;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $latestClips = Clip::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get()
            ->map(function (Clip $clip): array {
                $videoUrl = (string) $clip->video_url;

                return [
                    'id' => $clip->id,
                    'title' => $clip->title,
                    'description' => $clip->description,
                    'thumbnail_url' => $clip->thumbnail_url,
                    'video_url' => $videoUrl,
                    'embed_url' => $this->resolveEmbedUrl($videoUrl),
                    'published_at' => optional($clip->published_at)?->toDateString(),
                ];
            })
            ->all();

        $upcomingMatches = EsportMatch::query()
            ->whereIn('status', [
                EsportMatch::STATUS_SCHEDULED,
                EsportMatch::STATUS_LOCKED,
                EsportMatch::STATUS_LIVE,
            ])
            ->orderBy('starts_at')
            ->limit(3)
            ->get([
                'id',
                'team_a_name',
                'team_b_name',
                'starts_at',
                'status',
            ]);

        $leaderboard = UserProgress::query()
            ->with('user:id,name')
            ->orderByDesc('total_rank_points')
            ->limit(5)
            ->get(['user_id', 'total_rank_points']);

        return view('marketing.index', [
            'latestClips' => $latestClips,
            'upcomingMatches' => $upcomingMatches,
            'leaderboard' => $leaderboard,
        ]);
    }

    private function resolveEmbedUrl(string $videoUrl): ?string
    {
        if ($videoUrl === '') {
            return null;
        }

        if (Str::contains($videoUrl, 'youtube.com/embed/')) {
            return $videoUrl;
        }

        if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/)([\w\-]{6,})#i', $videoUrl, $matches) === 1) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        return null;
    }
}

