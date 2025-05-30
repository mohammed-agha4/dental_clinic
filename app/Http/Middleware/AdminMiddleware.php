<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->staff->role === 'admin') {
            return $next($request);
        }

        return redirect()->route('dashboard.index')->with('error', 'Unauthorized access');
    }
}
