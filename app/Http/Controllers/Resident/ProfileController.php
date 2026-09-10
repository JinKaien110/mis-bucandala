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
            'phase' => 'nullable|string|max:50',
            'address_line' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:80',
            'monthly_income' => 'nullable|string|max:80',
            'educational_attainment' => 'nullable|string|max:100',
            'pwd_status' => 'nullable|boolean',
            'solo_parent' => 'nullable|boolean',
            'indigent_status' => 'nullable|boolean',
            'four_ps_beneficiary' => 'nullable|boolean',
            'guardian_full_name' => 'nullable|string|max:150',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_contact_no' => 'nullable|string|max:30',
            'guardian_relationship' => 'nullable|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        foreach (['pwd_status', 'solo_parent', 'indigent_status', 'four_ps_beneficiary'] as $boolField) {
            if ($request->filled($boolField) || $request->has($boolField)) {
                $validated[$boolField] = $request->boolean($boolField);
            }
        }

        $resident->fill($validated);
        $resident->save();

        if ($request->hasFile('profile_picture')) {
            if ($resident->photo_path) {
                Storage::disk('public')->delete($resident->photo_path);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $resident->photo_path = $path;
            $resident->save();
        }

        $fullName = trim(($request->first_name ?? '') . ' ' . ($request->middle_name ?? '') . ' ' . ($request->last_name ?? ''));
        $user->update(['name' => $fullName]);

        return response()->json(['message' => 'Profile updated successfully!', 'success' => true]);
    }
}

