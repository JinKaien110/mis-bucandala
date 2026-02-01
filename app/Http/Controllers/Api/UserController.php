<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $users = User::query()
            ->when($q, fn($query) =>
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
            )
            ->orderBy('id', 'desc')
            ->get(['id','name','email','role','status','created_at']);

        return response()->json(['users' => $users]);
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'email' => ['required','email','max:255', 'unique:users,email'],
        'password' => ['required','string','min:8'],
        'role' => ['required', Rule::in(['admin','staff'])],
        'status' => ['nullable', Rule::in(['active','inactive'])],
    ]);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role' => $data['role'],
        'status' => $data['status'] ?? 'active',
    ]);

    
    if (!$request->expectsJson()) {
        return back()->with('success', 'User created successfully.');
    }

    
    return response()->json([
        'message' => 'User created',
        'user' => $user->only(['id','name','email','role','status'])
    ], 201);
}

    public function show(User $user)
    {
        return response()->json([
            'user' => $user->only(['id','name','email','role','status','created_at'])
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin','staff'])],
            'status' => ['required', Rule::in(['active','inactive'])],
        ]);

        $user->update($data);

        return response()->json(['message' => 'User updated', 'user' => $user->only(['id','name','email','role','status'])]);
    }

    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return response()->json(['message' => 'Status updated', 'status' => $user->status]);
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required','string','min:8'],
        ]);

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json(['message' => 'Password reset']);
    }
}
