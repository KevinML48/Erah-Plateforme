<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\UpdateProfileRequest;
use App\Models\Bet;
use App\Models\Duel;
use App\Models\PointsTransaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(EnsureUserProgressAction $ensureUserProgressAction): View
    {
        $user = auth()->user();
        $progress = $ensureUserProgressAction->execute($user)->load('league');

        $transactions = PointsTransaction::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $stats = $this->buildStats($user);

        return view('pages.profile.show', [
            'user' => $user,
            'progress' => $progress,
            'transactions' => $transactions,
            'stats' => $stats,
        ]);
    }

    public function update(
        UpdateProfileRequest $request,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $user = $request->user();
        $validated = $request->validated();
        $newAvatarPath = null;
        $oldAvatarPath = $user->avatar_path;

        if ($request->hasFile('avatar')) {
            $newAvatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        DB::transaction(function () use ($user, $validated, $newAvatarPath, $storeAuditLogAction) {
            $lockedUser = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedUser->name = (string) $validated['name'];
            $lockedUser->bio = $validated['bio'] ?? null;
            $lockedUser->twitter_url = $validated['twitter_url'] ?? null;
            $lockedUser->instagram_url = $validated['instagram_url'] ?? null;
            $lockedUser->tiktok_url = $validated['tiktok_url'] ?? null;
            $lockedUser->discord_url = $validated['discord_url'] ?? null;

            if ($newAvatarPath !== null) {
                $lockedUser->avatar_path = $newAvatarPath;
            }

            $lockedUser->save();

            $storeAuditLogAction->execute(
                action: 'profile.updated',
                actor: $lockedUser,
                target: $lockedUser,
                context: [
                    'updated_fields' => [
                        'name',
                        'bio',
                        'twitter_url',
                        'instagram_url',
                        'tiktok_url',
                        'discord_url',
                        'avatar_path' => $newAvatarPath !== null,
                    ],
                ],
            );
        });

        if ($newAvatarPath !== null && ! blank($oldAvatarPath) && $oldAvatarPath !== $newAvatarPath) {
            if (! str_starts_with((string) $oldAvatarPath, 'http://') && ! str_starts_with((string) $oldAvatarPath, 'https://')) {
                Storage::disk('public')->delete((string) $oldAvatarPath);
            }
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profil mis a jour.');
    }

    /**
     * @return array<string, int>
     */
    private function buildStats(User $user): array
    {
        return [
            'likes' => $user->clipLikes()->count(),
            'comments' => $user->clipComments()->count(),
            'duels' => Duel::query()->forUser($user->id)->count(),
            'bets' => Bet::query()->where('user_id', $user->id)->count(),
        ];
    }
}
