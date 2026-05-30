<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('user_id')) {
            session()->flash('flash', ['type' => 'warning', 'message' => 'Please log in to access this page.']);

            return redirect('/pages/login.php?redirect=' . urlencode($request->fullUrl()));
        }

        return $next($request);
    }
}