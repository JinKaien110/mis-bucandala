<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCaptain
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        // Check if user has admin role
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin role required.');
        }
        
        return $next($request);
    }
}