<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user(); // works if you are using auth middleware

        if (!$user) {
            abort(401);
        }

        $role = strtolower($user->role ?? '');

        $allowed = array_map(fn($r) => strtolower($r), $roles);

        if (!in_array($role, $allowed, true)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
