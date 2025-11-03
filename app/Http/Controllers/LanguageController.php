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
            return redirect()->back();
        }

        session(['locale' => $locale]);

        $currentRoute = Route::current();
        $routeName = $currentRoute ? $currentRoute->getName() : null;
        $parameters = $currentRoute ? $currentRoute->parameters() : [];

        unset($parameters['locale']);

        if ($routeName) {
            $baseRouteName = preg_replace('/^[a-z]{2}\./', '', $routeName);

            $newRouteName = $locale !== $defaultLocale ? "{$locale}.{$baseRouteName}" : $baseRouteName;

            if ($locale !== $defaultLocale) {
                $parameters = array_merge(['locale' => $locale], $parameters);
            }

            try {
                return redirect()->route($newRouteName, $parameters)
                    ->withCookie(cookie('locale', $locale, 525600));
            } catch (\Exception $e) {
            }
        }

        return redirect()->back()->withCookie(cookie('locale', $locale, 525600));
    }
}
