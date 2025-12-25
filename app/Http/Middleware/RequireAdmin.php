<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!isLoggedIn()) {
            return redirect('/login');
        }

        if (!isAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}