<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && strtolower($user->role) === 'admin') {
            abort(403, 'Admin users cannot access this page.');
        }

        return $next($request);
    }
}
