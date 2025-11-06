<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = array_keys(config('app.available_locales', ['en']));
        $defaultLocale = config('app.locale', 'en');

        $locale = $request->segment(1);

        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
            Cookie::queue('locale', $locale, 525600);

            return $next($request);
        } else {
            $sessionLocale = Session::get('locale');
            $cookieLocale = $request->cookie('locale');

            $preferredLocale = $sessionLocale ?: $cookieLocale ?: $defaultLocale;

            if (in_array($preferredLocale, $availableLocales)) {
                App::setLocale($preferredLocale);
                Session::put('locale', $preferredLocale);
            } else {
                App::setLocale($defaultLocale);
                Session::put('locale', $defaultLocale);
            }
        }

        return $next($request);
    }
}
