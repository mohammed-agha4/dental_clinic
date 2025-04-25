<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EagerLoadUserRelations
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            $request->user()->load('roles.abilities');
        }

        return $next($request);
    }
}
