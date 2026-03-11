<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->session()->get('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale', 'en');

        if (!in_array($locale, ['en', 'ar'], true)) {
            $locale = 'en';
        }

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);

        try {
            Carbon::setLocale($locale);
        } catch (\Throwable $e) {
            // Ignore locale issues for Carbon to avoid breaking requests.
        }

        return $next($request);
    }
}
