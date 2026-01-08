<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!isLoggedIn()) {
            return redirect('/login');
        }

        return $next($request);
    }
}