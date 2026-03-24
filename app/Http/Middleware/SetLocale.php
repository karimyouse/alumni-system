<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supported = ['en', 'ar'];

        $locale = session('locale');

        if (!is_string($locale) || !in_array($locale, $supported, true)) {
            $locale = $request->cookie('locale', config('app.locale', 'en'));
        }

        if (!is_string($locale) || !in_array($locale, $supported, true)) {
            $locale = config('app.locale', 'en');
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        try {
            Carbon::setLocale($locale);
        } catch (\Throwable $e) {
            
        }

        return $next($request);
    }
}
