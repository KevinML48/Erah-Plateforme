<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AddClipCommentAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $user, Clip $clip, string $body): ClipComment
    {
        return DB::transaction(function () use ($user, $clip, $body) {
            $lockedClip = Clip::query()->whereKey($clip->id)->lockForUpdate()->firstOrFail();

            if (! $lockedClip->is_published || $lockedClip->published_at === null) {
                throw new RuntimeException('Clip is not published.');
            }

            $comment = ClipComment::query()->create([
                'clip_id' => $lockedClip->id,
                'user_id' => $user->id,
                'body' => trim($body),
            ]);

            $lockedClip->increment('comments_count');

            $this->storeAuditLogAction->execute(
                action: 'clips.comment.added',
                actor: $user,
                target: $comment,
                context: [
                    'clip_id' => $lockedClip->id,
                    'user_id' => $user->id,
                ],
            );

            return $comment->fresh(['user:id,name']);
        });
    }
}
