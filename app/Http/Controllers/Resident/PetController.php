<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();
        
        $pets = [];
        if ($resident) {
            $pets = Pet::where('resident_id', $resident->id)->get();
        }
        
        return view('resident.pets', compact('user', 'resident', 'pets'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (!$resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:dog,cat,bird,other',
            'breed' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,unknown',
            'vaccination_status' => 'nullable|in:vaccinated,unvaccinated,partial',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['resident_id'] = $resident->id;
        $validated['registration_date'] = now();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pet_photos', 'public');
            $validated['photo'] = $path;
        }

        Pet::create($validated);

        return response()->json(['message' => 'Pet registered successfully!', 'success' => true]);
    }

    public function update(Request $request, Pet $pet)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (!$resident || $pet->resident_id !== $resident->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:dog,cat,bird,other',
            'breed' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,unknown',
            'vaccination_status' => 'nullable|in:vaccinated,unvaccinated,partial',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($pet->photo) {
                Storage::disk('public')->delete($pet->photo);
            }
            $path = $request->file('photo')->store('pet_photos', 'public');
            $validated['photo'] = $path;
        }

        $pet->update($validated);

        return response()->json(['message' => 'Pet updated successfully!', 'success' => true]);
    }

    public function destroy(Pet $pet)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (!$resident || $pet->resident_id !== $resident->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete photo
        if ($pet->photo) {
            Storage::disk('public')->delete($pet->photo);
        }

        $pet->delete();

        return response()->json(['message' => 'Pet removed successfully!', 'success' => true]);
    }
}