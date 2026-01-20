<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) abort(403);

        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $one) {
                $one = trim($one);
                if ($one !== '') $allowed[] = $one;
            }
        }

        if (!in_array($user->role, $allowed, true)) abort(403);

        return $next($request);
    }
}
