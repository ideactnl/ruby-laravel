<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

                // Remove any existing locale prefix (e.g., /nl/, /en/)
                // Only remove actual locale prefixes, not just any 2-letter combination
                $cleanPath = preg_replace('/^\/nl\//', '/', $refererPath); // Remove /nl/ prefix
                $cleanPath = preg_replace('/^\/en\//', '/', $cleanPath);   // Remove /en/ prefix if it exists

                // Ensure clean path starts with /
                if (! str_starts_with($cleanPath, '/')) {
                    $cleanPath = '/'.$cleanPath;
                }

                // Add locale prefix only for non-default locales
                // Debug: let's see what's happening
                if ($locale === 'en' && $defaultLocale === 'en') {
                    // English is default, no prefix
                    $newPath = $cleanPath;
                } elseif ($locale === 'nl') {
                    // Dutch needs prefix
                    $newPath = '/nl'.$cleanPath;
                } else {
                    // Fallback
                    $newPath = $cleanPath;
                }

                return redirect($newPath)->withCookie(cookie('locale', $locale, 525600));

            } catch (\Exception $e) {
                // If URL parsing fails, fall back to route-based redirect
            }
        }

        // Fallback: redirect to dashboard with proper locale
        try {
            $dashboardRoute = $locale !== $defaultLocale ? "{$locale}.participant.dashboard" : 'participant.dashboard';

            return redirect()->route($dashboardRoute)->withCookie(cookie('locale', $locale, 525600));
        } catch (\Exception $e) {
            // Final fallback
            return redirect('/')->withCookie(cookie('locale', $locale, 525600));
        }
    }
}
