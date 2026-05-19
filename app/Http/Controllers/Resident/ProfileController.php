<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();
        
        return view('resident.profile', compact('user', 'resident'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (!$resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'contact_no' => 'nullable|string|max:30',
            'address_line' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:80',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update resident fields
        $resident->update($request->except('profile_picture'));

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old image if exists
            if ($resident->profile_picture) {
                Storage::disk('public')->delete($resident->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $resident->update(['profile_picture' => $path]);
        }

        // Update user name
        $fullName = trim(($request->first_name ?? '') . ' ' . ($request->middle_name ?? '') . ' ' . ($request->last_name ?? ''));
        $user->update(['name' => $fullName]);

        return response()->json(['message' => 'Profile updated successfully!', 'success' => true]);
    }
}