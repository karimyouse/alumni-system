<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $expected = $role === 'admin' ? 'super_admin' : $role;

        if (($request->user()->role ?? null) !== $expected) {
            abort(403);
        }

        return $next($request);
    }
}
