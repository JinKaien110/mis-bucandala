<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        $userRole = strtolower($user->role ?? '');
        
        // Map role names: admin = captain, staff = secretary
        $roleMapping = [
            'admin' => 'admin',      // captain
            'staff' => 'staff',      // secretary
            'secretary' => 'staff',  // synonym for staff role
            'clerk' => 'clerk',
            'blotter' => 'blotter',
            'readonly' => 'readonly',
        ];
        
        // Normalize the user's role
        $normalizedRole = $roleMapping[$userRole] ?? $userRole;
        
        // Check if user's role is in the allowed roles
        $allowedRoles = array_map(fn($r) => strtolower($r), $roles);
        
        if (!in_array($normalizedRole, $allowedRoles, true)) {
            abort(403, 'Unauthorized access. You do not have permission to access this resource.');
        }
        
        return $next($request);
    }
}