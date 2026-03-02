<?php

namespace App\Policies;

use App\Models\ClipComment;
use App\Models\User;

class CommentPolicy
{
    public function delete(User $user, ClipComment $comment): bool
    {
        return $user->role === User::ROLE_ADMIN || $comment->user_id === $user->id;
    }
}
