<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\AdminAuditLog;
use App\Models\User;

class AdminAuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('audit.view') || $user->isAdmin();
    }

    public function view(User $user, AdminAuditLog $adminAuditLog): bool
    {
        return $this->viewAny($user);
    }
}
