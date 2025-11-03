<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class LocaleHelper
{
    /**
     * Generate a localized route URL
     */
    public static function route(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale = $locale ?: App::getLocale();
        $availableLocales = array_keys(config('app.available_locales', ['en']));

        if ($locale !== config('app.locale') && in_array($locale, $availableLocales)) {
            $parameters = array_merge(['locale' => $locale], $parameters);
        }

        return route($name, $parameters);
    }

    /**
     * Get the current locale
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get available locales
     */
    public static function getAvailableLocales(): array
    {
        return config('app.available_locales', ['en' => 'English']);
    }

    /**
     * Generate URL for switching language
     */
    public static function switchLanguageUrl(string $locale): string
    {
        $currentRoute = Route::current();

        if (! $currentRoute) {
            return url($locale);
        }

        $routeName = $currentRoute->getName();
        $parameters = $currentRoute->parameters();

        unset($parameters['locale']);

        if ($locale !== config('app.locale')) {
            $parameters = array_merge(['locale' => $locale], $parameters);
        }

        try {
            return route($routeName, $parameters);
        } catch (\Exception $e) {
            return url($locale);
        }
    }
}
