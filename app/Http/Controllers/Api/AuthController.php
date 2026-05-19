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

    return response()->json([
        'message' => 'Logged in',
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
