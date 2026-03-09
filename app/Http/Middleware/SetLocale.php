<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale', 'en'));

        if (!in_array($locale, ['en','ar'], true)) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        // Makes diffForHumans/month names respect the locale
        try {
            Carbon::setLocale($locale);
        } catch (\Throwable $e) {}

        return $next($request);
    }
}
