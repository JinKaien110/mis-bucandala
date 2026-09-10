<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AbilityMiddleware
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $user = auth()->user();

        if (!$user || !$user->admin) {
            abort(403, 'Unauthorized.');
        }

        $position = $user->admin->position;

        $allowed = match ($ability) {

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */

            'view-dashboard' =>
                in_array($position, [
                    'Barangay Captain',
                    'Barangay Secretary',
                    'Barangay Clerk'
                ]),

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            'manage-users' =>
                in_array($position, [
                    'Barangay Captain',
                    'Barangay Secretary',
                    'Barangay Clerk'
                ]),

            'create-users' =>
                in_array($position, [
                    'Barangay Captain',
                    'Barangay Secretary',
                    'Barangay Clerk'
                ]),

            'view-users' =>
                in_array($position, [
                    'Barangay Captain',
                    'Barangay Secretary',
                    'Barangay Clerk'
                ]),

            /*
            |--------------------------------------------------------------------------
            | Reports / Analytics
            |--------------------------------------------------------------------------
            */

            'view-reports' =>
                in_array($position, [
                    'Barangay Captain',
                    'Barangay Secretary',
                    'Barangay Clerk'
                ]),

            /*
            |--------------------------------------------------------------------------
            | Core Records
            |--------------------------------------------------------------------------
            */

            'view-core-records' =>
                in_array($position, [
                    'Barangay Captain',
                    'Barangay Secretary',
                    'Barangay Clerk'
                ]),

            /*
            |--------------------------------------------------------------------------
            | Households
            |--------------------------------------------------------------------------
            */

            'view-households' =>
                in_array($position, [
                    'Barangay Captain',
                    'Barangay Secretary',
                    'Barangay Clerk'
                ]),

            /*
            |--------------------------------------------------------------------------
            | Document Requests
            |--------------------------------------------------------------------------
            */

            'view-documents' =>
                in_array($position, [
                    'Barangay Captain',
                    'Barangay Secretary',
                    'Barangay Clerk'
                ]),

            /*
            |--------------------------------------------------------------------------
            | Payments
            |--------------------------------------------------------------------------
            */

            'view-payments' =>
                in_array($position, [
                    'Barangay Captain',
                    'Barangay Treasurer',
                    'Barangay Clerk'
                ]),

            /*
            |--------------------------------------------------------------------------
            | Blotters & Cases
            |--------------------------------------------------------------------------
            */

            'view-blotters' =>
                in_array($position, [
                    'Barangay Captain',
                    'Lupon Member',
                ]),

            /*
            |--------------------------------------------------------------------------
            | Community & Administration
            |--------------------------------------------------------------------------
            */

            'view-community' =>
                in_array($position, [
                    'Barangay Captain',
                    'Barangay Secretary',
                ]),

            default => false,
        };

        if (!$allowed) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}