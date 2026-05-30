<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeCsrfToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->request->has('_token') && $request->request->has('csrf_token')) {
            $request->request->set('_token', $request->request->get('csrf_token'));
        }

        return $next($request);
    }
}