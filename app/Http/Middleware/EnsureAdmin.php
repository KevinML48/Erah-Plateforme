<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $hasPermissionTables = Schema::hasTable('permissions')
            && Schema::hasTable('model_has_permissions')
            && Schema::hasTable('model_has_roles');

        $canAccessAdmin = $user
            && (
                ($hasPermissionTables && $user->can('admin.access'))
                || $user->hasAnyRole(['super_admin', 'admin', 'moderator', 'logistics', 'analyst'])
                || $user->isAdmin()
            );

        abort_unless($canAccessAdmin, 403);

        return $next($request);
    }
}
