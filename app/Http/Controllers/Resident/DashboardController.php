<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\DocumentRequest;
use App\Models\Pet;
use App\Models\Household;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get resident profile linked to user
        $resident = Resident::where('user_id', $user->id)->first();
        
        // Get document requests for this resident
        $documentRequests = [];
        $myPets = [];
        $householdMembers = [];
        $household = null;
        
        if ($resident) {
            $documentRequests = DocumentRequest::where('resident_id', $resident->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Get pets for this resident
            $myPets = Pet::where('resident_id', $resident->id)->get();
            
            // Get household members
            if ($resident->household_id) {
                $household = Household::find($resident->household_id);
                $householdMembers = Resident::where('household_id', $resident->household_id)->get();
            }
        }
        
        // Extract first name from user name
        $firstName = explode(' ', $user->name)[0];
        
        return view('resident.dashboard', compact('user', 'resident', 'documentRequests', 'firstName', 'myPets', 'household', 'householdMembers'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Get resident profile
        $resident = Resident::where('user_id', $user->id)->first();
        
        if (!$resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }
        
        // Validate request
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'contact_no' => 'nullable|string|max:30',
            'address_line' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:80',
        ]);
        
        // Update resident
        $resident->update($validated);
        
        // Update user name if needed
        if ($request->filled('first_name') || $request->filled('last_name')) {
            $fullName = trim(($request->first_name ?? '') . ' ' . ($request->middle_name ?? '') . ' ' . ($request->last_name ?? ''));
            $user->update(['name' => $fullName]);
        }
        
        return response()->json(['message' => 'Profile updated successfully.']);
    }
}