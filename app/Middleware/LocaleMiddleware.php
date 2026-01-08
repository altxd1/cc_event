<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/**
 * LocaleMiddleware checks for a 'lang' parameter in the query string
 * or a previously stored locale in the session. When found, it sets
 * the application locale accordingly. This allows users to switch
 * between supported languages.
 */
class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // If the request has a language parameter, store it in the session
        $lang = $request->query('lang');
        if ($lang) {
            Session::put('locale', $lang);
        }

        // Use the stored locale if available, otherwise default to en
        $locale = Session::get('locale', 'en');
        App::setLocale($locale);

        return $next($request);
    }
}