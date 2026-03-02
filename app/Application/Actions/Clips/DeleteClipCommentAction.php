<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeleteClipCommentAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $actor, ClipComment $comment): Clip
    {
        return DB::transaction(function () use ($actor, $comment) {
            $lockedComment = ClipComment::query()
                ->whereKey($comment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($actor->role !== User::ROLE_ADMIN && $lockedComment->user_id !== $actor->id) {
                throw new AuthorizationException('You are not allowed to delete this comment.');
            }

            $clip = Clip::query()
                ->whereKey($lockedComment->clip_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $clip->is_published || $clip->published_at === null) {
                throw new RuntimeException('Clip is not published.');
            }

            $commentId = $lockedComment->id;
            $commentUserId = $lockedComment->user_id;
            $lockedComment->delete();

            Clip::query()
                ->whereKey($clip->id)
                ->update([
                    'comments_count' => DB::raw('CASE WHEN comments_count > 0 THEN comments_count - 1 ELSE 0 END'),
                    'updated_at' => now(),
                ]);

            $this->storeAuditLogAction->execute(
                action: 'clips.comment.deleted',
                actor: $actor,
                target: $clip,
                context: [
                    'clip_id' => $clip->id,
                    'comment_id' => $commentId,
                    'comment_user_id' => $commentUserId,
                ],
            );

            return $clip->fresh();
        });
    }
}
