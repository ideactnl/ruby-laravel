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
        $available = array_keys(config('app.available_locales', ['en']));

        if (! in_array($locale, $available)) {
            $locale = config('app.locale');
        }

        session(['locale' => $locale]);
        cookie()->queue('locale', $locale, 525600);

        return back();
    }
}
