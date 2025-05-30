<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{

    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!isset(Auth::user()->staff) || !in_array(Auth::user()->staff->role, $roles)) {
            return redirect()->route('dashboard.appointments.index')
                ->with('error', 'You are not authorized to access this area.');
        }

        return $next($request);
    }
}
