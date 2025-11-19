<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $cookieLocale = $request->cookie('locale');
        $sessionLocale = session('locale');
        $default = config('app.locale', 'en');

        $locale = $cookieLocale ?? $sessionLocale ?? $default;

        App::setLocale($locale);

        session(['locale' => $locale]);

        return $next($request);
    }
}
