<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyPlatformSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), geolocation=(), microphone=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($this->shouldNoIndex($request)) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        }

        return $response;
    }

    private function shouldNoIndex(Request $request): bool
    {
        if ($request->is(
            'api/*',
            'login',
            'register',
            'forgot-password',
            'reset-password',
            'reset-password/*',
            'verify-email',
            'verify-email/*',
            'confirm-password',
            'console/admin*',
            'dev*',
        )) {
            return true;
        }

        foreach ($request->route()?->gatherMiddleware() ?? [] as $middleware) {
            if (
                str_starts_with($middleware, 'auth')
                || str_contains($middleware, 'Authenticate')
                || $middleware === 'admin'
                || str_contains($middleware, 'EnsureAdminRole')
                || $middleware === 'local.only'
                || str_contains($middleware, 'LocalOnly')
            ) {
                return true;
            }
        }

        return false;
    }
}
