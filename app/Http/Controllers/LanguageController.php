<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch the application language and set persistent cookie
     */
    public function switch(Request $request)
    {
        $locale = $request->input('locale');
        $availableLocales = array_keys(config('app.available_locales', ['en']));
        $defaultLocale = config('app.locale', 'en');

        if (! in_array($locale, $availableLocales)) {
            return redirect()->back()->withCookie(cookie('locale', $defaultLocale, 525600));
        }

        session(['locale' => $locale]);

        $referer = $request->header('referer');

        if ($referer) {
            try {
                $refererPath = parse_url($referer, PHP_URL_PATH);

                $cleanPath = preg_replace('/^\/nl\//', '/', $refererPath);
                $cleanPath = preg_replace('/^\/en\//', '/', $cleanPath);

                if (! str_starts_with($cleanPath, '/')) {
                    $cleanPath = '/'.$cleanPath;
                }

                if ($locale === 'en' && $defaultLocale === 'en') {
                    $newPath = $cleanPath;
                } elseif ($locale === 'nl') {
                    $newPath = '/nl'.$cleanPath;
                } else {
                    $newPath = $cleanPath;
                }

                return redirect($newPath)->withCookie(cookie('locale', $locale, 525600));

            } catch (\Exception $e) {
            }
        }

        try {
            $dashboardRoute = $locale !== $defaultLocale ? "{$locale}.participant.dashboard" : 'participant.dashboard';

            return redirect()->route($dashboardRoute)->withCookie(cookie('locale', $locale, 525600));
        } catch (\Exception $e) {
            return redirect('/')->withCookie(cookie('locale', $locale, 525600));
        }
    }
}
