<?php

namespace App\Policies;

use App\Models\Clip;
use App\Models\User;

class ClipPolicy
{
    public function create(User $user): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    public function update(User $user, Clip $clip): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    public function delete(User $user, Clip $clip): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    public function publish(User $user, Clip $clip): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }
}
