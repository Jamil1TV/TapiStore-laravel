<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacySessionBridge
{
    public function handle(Request $request, Closure $next): Response
    {
        if (function_exists('syncLegacySessionGlobals')) {
            syncLegacySessionGlobals();
        }

        return $next($request);
    }
}