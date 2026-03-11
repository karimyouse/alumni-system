<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        $supported = ['en', 'ar'];
        if (!in_array($locale, $supported)) {
            $locale = config('app.locale', 'en');
        }
        session(['locale' => $locale]);
        $referer = $request->headers->get('referer');
        if ($referer) {
            return redirect($referer);
        }
        return redirect()->back();
    }
}
