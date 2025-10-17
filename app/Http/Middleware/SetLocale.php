<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
        $availableLocales = ['en', 'fr', 'es'];

        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
        } else {
            App::setLocale(config('app.locale'));
        }
        return $next($request);
    }
}
