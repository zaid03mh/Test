<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!in_array($request->user()->role->name, $roles)) {
            // Redirect or handle access denial here
            return redirect('/');
        }
        return $next($request);
    }
}

