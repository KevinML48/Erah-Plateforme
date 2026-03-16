<?php

namespace App\Application\Actions\Users;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\ClubReview;
use App\Models\User;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModeratePublicProfileAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(User $target, User $actor, array $data, ?string $ip = null): void
    {
        $avatarToDelete = null;

        DB::transaction(function () use ($target, $actor, $data, $ip, &$avatarToDelete): void {
            $lockedUser = User::query()
                ->whereKey($target->id)
                ->lockForUpdate()
                ->firstOrFail();

            $originalName = (string) $lockedUser->name;
            $clearSocialLinks = (bool) ($data['clear_social_links'] ?? false);
            $removeAvatar = (bool) ($data['remove_avatar'] ?? false);

            $lockedUser->name = (string) $data['name'];
            $lockedUser->bio = $data['bio'] ?? null;
            $lockedUser->twitter_url = $clearSocialLinks ? null : ($data['twitter_url'] ?? null);
            $lockedUser->instagram_url = $clearSocialLinks ? null : ($data['instagram_url'] ?? null);
            $lockedUser->tiktok_url = $clearSocialLinks ? null : ($data['tiktok_url'] ?? null);
            $lockedUser->discord_url = $clearSocialLinks ? null : ($data['discord_url'] ?? null);

            if ($removeAvatar && filled($lockedUser->avatar_path)) {
                $avatarToDelete = (string) $lockedUser->avatar_path;
                $lockedUser->avatar_path = null;
            }

            $lockedUser->save();

            $supporterProfile = $lockedUser->supportPublicProfile()->first();
            if ($supporterProfile && (blank($supporterProfile->display_name) || $supporterProfile->display_name === $originalName)) {
                $supporterProfile->display_name = $lockedUser->name;
                $supporterProfile->save();
            }

            $reviewAction = 'unchanged';
            if (Schema::hasTable('club_reviews')) {
                $review = $lockedUser->clubReview()->first();

                if ($review instanceof ClubReview) {
                    if ((bool) ($data['delete_review'] ?? false)) {
                        $review->delete();
                        $reviewAction = 'deleted';
                    } elseif (filled($data['review_status'] ?? null)) {
                        $review->status = (string) $data['review_status'];

                        if ($review->status === ClubReview::STATUS_PUBLISHED) {
                            $review->published_at = now();
                        }

                        $review->save();
                        $reviewAction = 'status:'.$review->status;
                    }
                }
            }

            $this->storeAuditLogAction->execute(
                action: 'users.profile_moderated',
                actor: $actor,
                target: $lockedUser,
                context: [
                    'ip' => $ip,
                    'updated_fields' => [
                        'name',
                        'bio',
                        'twitter_url' => ! $clearSocialLinks,
                        'instagram_url' => ! $clearSocialLinks,
                        'tiktok_url' => ! $clearSocialLinks,
                        'discord_url' => ! $clearSocialLinks,
                        'clear_social_links' => $clearSocialLinks,
                        'remove_avatar' => $removeAvatar,
                    ],
                    'review_action' => $reviewAction,
                ],
            );
        });

        MediaStorage::delete($avatarToDelete);
    }
}
