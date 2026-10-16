<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    

public function login(\Illuminate\Http\Request $request)
{
    $credentials = $request->validate([
        'email' => ['required','email'],
        'password' => ['required'],
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 422);
    }

    $request->session()->regenerate();

    $intended = $request->session()->pull('url.intended');
    $redirectTo = '/auth/login';
    if ($request->user()->role === 'resident') {
        $redirectTo = route('resident.dashboard');
        if (is_string($intended)) {
            $path = parse_url($intended, PHP_URL_PATH);
            $query = parse_url($intended, PHP_URL_QUERY);
            if (is_string($path) && str_starts_with($path, '/resident/')) {
                $redirectTo = url($path . ($query ? '?' . $query : ''));
            }
        }
    } else {
        $request->session()->forget('url.intended');
    }

    return response()->json([
        'message' => 'Logged in',
        'redirect' => $redirectTo,
        'user' => [
            'id' => $request->user()->id,
            'email' => $request->user()->email,
            'role' => $request->user()->role,
        ],
    ]);
}

public function me(\Illuminate\Http\Request $request)
{
    return response()->json(['user' => $request->user()]);
}

public function logout(\Illuminate\Http\Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json(['message' => 'Logged out']);
}

}
