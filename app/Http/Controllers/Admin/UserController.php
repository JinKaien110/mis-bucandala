<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'role' => ['nullable', 'in:admin,clerk,blotter,readonly'],
            'status' => ['nullable', 'in:active,inactive'],
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int)($validated['limit'] ?? 10);

        $q = User::query()
            ->orderByDesc('id');

        if (!empty($validated['role'])) {
            $q->where('role', $validated['role']);
        }

        if (!empty($validated['status'])) {
            $q->where('status', $validated['status']);
        }

        if (!empty($validated['search'])) {
            $s = trim($validated['search']);
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $q->paginate($limit)->withQueryString();

        $stats = [
            'total_users' => User::count(),
            'admin_count' => User::where('role', 'admin')->count(),
            'clerk_count' => User::where('role', 'clerk')->count(),
            'active_count' => User::where('status', 'active')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,staff'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['required', 'string', 'max:255'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['registered_via'] = 'admin_create';
        $validated['status'] = 'active';

        $user = User::create([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'registered_via' => $validated['registered_via'],
        ]);

        Admin::create([
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
            'position' => $validated['position'],
            'status' => 'active',
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,clerk,blotter,readonly'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Password reset successfully.');
    }

    public function toggleStatus(User $user)
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        
        $user->update(['status' => $newStatus]);

        $message = $newStatus === 'active' 
            ? 'User activated successfully.' 
            : 'User deactivated successfully.';

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', $message);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}