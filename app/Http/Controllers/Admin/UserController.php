<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\BarangayOfficial;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([

        'role' => ['nullable', 'in:admin,clerk,lupon,readonly'],
        'status' => ['nullable', 'in:active,inactive'],
        'search' => ['nullable', 'string', 'max:255'],
        'page' => ['nullable', 'integer', 'min:1'],
        'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
    ]);

    $limit = (int) ($validated['limit'] ?? 10);

    $q = User::query()->orderByDesc('id');

    // Role filtering
    if (!empty($validated['role'])) {
            if ($validated['role'] === 'lupon') {
                // Lupon members are staff users whose admins.position = "Lupon Member"
                $q->where('role', 'staff')
                  ->whereHas('admin', function ($query) {
                      $query->where('position', 'Lupon Member');
                  });
            } elseif ($validated['role'] === 'secretary') {
                // Secretary staff are staff users whose admins.position = "Barangay Secretary"
                $q->where('role', 'staff')
                  ->whereHas('admin', function ($query) {
                      $query->where('position', 'Barangay Secretary');
                  });
            } else {
                $q->where('role', $validated['role']);
            }
        }


    // Status filtering
    if (!empty($validated['status'])) {
        $q->where('status', $validated['status']);
    }

    // Search filtering
    if (!empty($validated['search'])) {
        $search = trim($validated['search']);

        $q->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Paginated users
    $users = $q->paginate($limit)->withQueryString();


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    | These follow the current status/search filters but are not affected
    | by the selected role filter.
    */

    $statsQuery = User::query();

    if (!empty($validated['status'])) {
        $statsQuery->where('status', $validated['status']);
    }

    if (!empty($validated['search'])) {
        $search = trim($validated['search']);

        $statsQuery->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $luponCountQuery = clone $statsQuery;

    $luponCountQuery
        ->where('role', 'staff')
        ->whereHas('admin', function ($query) {
            $query->where('position', 'Lupon Member');
        });


    $stats = [
        'total_users' => (clone $statsQuery)->count(),

        'admin_count' => (clone $statsQuery)
            ->where('role', 'admin')
            ->count(),

        'clerk_count' => (clone $statsQuery)
            ->where('role', 'clerk')
            ->count(),

        'lupon_count' => $luponCountQuery->count(),

        'active_count' => (clone $statsQuery)
            ->where('status', 'active')
            ->count(),
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
            'role' => ['required', 'in:admin,staff,lupon,clerk,readonly'],
            'contact_no' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:255'],

                'barangay_official_id' => [
                    'nullable',
                    'exists:barangay_officials,id',
                ],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['registered_via'] = 'admin_create';
        $validated['status'] = 'active';

        $official = null;

if (!empty($validated['barangay_official_id'])) {

    $official = BarangayOfficial::whereHas('term', function ($q) {
       $q->where('term_end', '>=', now());
    })->findOrFail($validated['barangay_official_id']);

    // Prevent duplicate accounts
    if (Admin::where('barangay_official_id', $official->id)->exists()) {

        return back()
            ->withErrors([
                'barangay_official_id' => 'This barangay official already has a user account.'
            ])
            ->withInput();
    }

}

        $user = User::create([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => auth()->user()->role === 'staff'
            ? 'staff'
            : $validated['role'],
            'status' => $validated['status'],
            'registered_via' => $validated['registered_via'],
        ]);


        Admin::create([

    'user_id' => $user->id,

    'barangay_official_id' => $official?->id,

    'first_name' => $validated['first_name'],

    'last_name' => $validated['last_name'],

    'contact_no' => $validated['contact_no'] ?? null,

    'position' => $validated['position'],


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
            'role' => ['required', 'in:admin,clerk,lupon,readonly'],
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
            'password' => Hash::make($validated['password']),
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

