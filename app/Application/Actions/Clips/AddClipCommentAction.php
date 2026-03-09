<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\User;
use App\Services\ClipRewardService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AddClipCommentAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction,
        private readonly ClipRewardService $clipRewardService,
        private readonly NotifyAction $notifyAction
    ) {
    }

    public function execute(User $user, Clip $clip, string $body, ?int $parentCommentId = null): ClipComment
    {
        return DB::transaction(function () use ($user, $clip, $body, $parentCommentId) {
            $lockedClip = Clip::query()->whereKey($clip->id)->lockForUpdate()->firstOrFail();

            if (! $lockedClip->is_published || $lockedClip->published_at === null) {
                throw new RuntimeException('Clip is not published.');
            }

            $parentComment = null;
            if ($parentCommentId !== null) {
                $parentComment = ClipComment::query()
                    ->where('clip_id', $lockedClip->id)
                    ->whereKey($parentCommentId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($parentComment->parent_id !== null) {
                    throw new RuntimeException('Maximum reply depth reached.');
                }
            }

            $comment = ClipComment::query()->create([
                'clip_id' => $lockedClip->id,
                'parent_id' => $parentComment?->id,
                'user_id' => $user->id,
                'body' => trim($body),
                'status' => ClipComment::STATUS_PUBLISHED,
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

            $this->clipRewardService->rewardComment($user, $lockedClip, $comment);

            if ($parentComment && (int) $parentComment->user_id !== (int) $user->id) {
                $this->notifyAction->execute(
                    user: $parentComment->user()->firstOrFail(),
                    category: NotificationCategory::COMMENT->value,
                    title: 'Nouvelle reponse',
                    message: $user->name.' a repondu a votre commentaire.',
                    data: [
                        'clip_id' => $lockedClip->id,
                        'comment_id' => $comment->id,
                        'parent_comment_id' => $parentComment->id,
                    ],
                );
            } elseif ($lockedClip->creator && (int) $lockedClip->creator->id !== (int) $user->id) {
                $this->notifyAction->execute(
                    user: $lockedClip->creator,
                    category: NotificationCategory::COMMENT->value,
                    title: 'Nouveau commentaire',
                    message: $user->name.' a commente votre clip.',
                    data: [
                        'clip_id' => $lockedClip->id,
                        'comment_id' => $comment->id,
                    ],
                );
            }

            return $comment->fresh(['user:id,name', 'parent']);
        });
    }
}
