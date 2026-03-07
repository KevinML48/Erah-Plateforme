<?php

namespace App\Http\Middleware;

use App\Services\SupporterAccessResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSupporterActive
{
    public function __construct(
        private readonly SupporterAccessResolver $supporterAccessResolver
    ) {
    }

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->supporterAccessResolver->hasActiveSupport($request->user())) {
            abort(403, 'Supporter actif requis.');
        }

        return $next($request);
    }
}
