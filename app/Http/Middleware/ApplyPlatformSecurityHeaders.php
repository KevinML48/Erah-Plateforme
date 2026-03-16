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
        $isPrivateHtmlResponse = $this->shouldDisableCaching($request, $response);

        $response->headers->set('Content-Security-Policy', "base-uri 'self'; frame-ancestors 'self'; form-action 'self' https://checkout.stripe.com; object-src 'none'");
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-site');
        $response->headers->set('Origin-Agent-Cluster', '?1');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), geolocation=(), microphone=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($this->shouldNoIndex($request)) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        }

        if ($isPrivateHtmlResponse) {
            $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }

    private function shouldNoIndex(Request $request): bool
    {
        if ($request->is(
            'api/*',
            'console/*',
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

    private function shouldDisableCaching(Request $request, Response $response): bool
    {
        if (! $request->isMethodCacheable()) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        if (! str_contains($contentType, 'text/html')) {
            return false;
        }

        return $request->user() !== null || $this->shouldNoIndex($request);
    }
}
