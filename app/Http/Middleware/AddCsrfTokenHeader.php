<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddCsrfTokenHeader
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only add if session exists (web middleware)
        if ($request->hasSession()) {
            $response->headers->set('X-CSRF-TOKEN', csrf_token());
        }

        return $response;
    }
}
