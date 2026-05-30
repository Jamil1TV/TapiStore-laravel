<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('user_id') || session('user_role') !== 'admin') {
            session()->flash('flash', ['type' => 'error', 'message' => 'Access denied. Admin privileges required.']);

            return redirect('/pages/login.php');
        }

        return $next($request);
    }
}