<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\HouseholdJoinRequest;
use App\Models\HouseholdMember;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HouseholdController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        $household = null;
        $members = [];
        $joinRequests = [];
        $pendingRequestsCount = 0;

        if ($resident && $resident->household_id) {
            $household = Household::with(['members.resident'])->find($resident->household_id);
            if ($household) {
                // If resident is head of household, show pending join requests
                if ($household->head_resident_id === $resident->id || ! $household->head_resident_id) {
                    $joinRequests = HouseholdJoinRequest::where('household_id', $household->id)
                        ->where('status', 'pending')
                        ->with(['resident', 'responder'])
                        ->latest()
                        ->get();
                    $pendingRequestsCount = $joinRequests->count();
                }
            }
        }

        return view('resident.household', compact('user', 'resident', 'household', 'joinRequests', 'pendingRequestsCount'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (! $resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }

        $validated = $request->validate([
            'address_line' => 'required|string|max:255',
            'phase' => 'nullable|integer|min:1',
            'contact_no' => 'nullable|string|max:20',
            'household_type' => 'nullable|string|max:50',
            'homeownership_type' => 'nullable|string|max:50',
            'house_type' => 'nullable|string|max:50',
            'has_toilet' => 'nullable|boolean',
            'has_bathroom' => 'nullable|boolean',
            'has_kitchen' => 'nullable|boolean',
            'monthly_income_range' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:50',
            'primary_income_source' => 'nullable|string|max:150',
            'is_4ps_beneficiary' => 'nullable|boolean',
            'is_indigent' => 'nullable|boolean',
            'has_pregnant_member' => 'nullable|boolean',
            'has_senior_citizen' => 'nullable|boolean',
            'has_pwd' => 'nullable|boolean',
            'has_chronic_illness' => 'nullable|boolean',
            'disaster_risk_level' => 'nullable|string|max:50',
            'barangay_program_participation' => 'nullable|string',
        ]);

        // Generate household code (HH-YYYY-NNNNNNN)
        $year = date('Y');
        $latest = Household::whereYear('created_at', $year)->latest()->first();
        $nextNumber = 1;
        if ($latest && preg_match('/HH-\d{4}-(\d{7})/', $latest->household_code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        }
        $householdCode = sprintf('HH-%s-%07d', $year, $nextNumber);

        $household = Household::create([
            'household_code' => $householdCode,
            'address_line' => $validated['address_line'],
            'phase' => $validated['phase'],
            'street' => $validated['street'] ?? null,
            'contact_no' => $validated['contact_no'],
            'household_type' => $validated['household_type'],
            'homeownership_type' => $validated['homeownership_type'],
            'house_type' => $validated['house_type'],
            'has_toilet' => $request->boolean('has_toilet'),
            'has_bathroom' => $request->boolean('has_bathroom'),
            'has_kitchen' => $request->boolean('has_kitchen'),
            'has_garage' => $request->boolean('has_garage'),
            'has_electricity' => $request->boolean('has_electricity'),
            'monthly_income_range' => $validated['monthly_income_range'] ?? null,
            'employment_status' => $validated['employment_status'] ?? null,
            'primary_income_source' => $validated['primary_income_source'] ?? null,
            'is_4ps_beneficiary' => $validated['is_4ps_beneficiary'] ?? false,
            'is_indigent' => $validated['is_indigent'] ?? false,
            'has_pregnant_member' => $validated['has_pregnant_member'] ?? false,
            'has_senior_citizen' => $validated['has_senior_citizen'] ?? false,
            'has_pwd' => $validated['has_pwd'] ?? false,
            'has_chronic_illness' => $validated['has_chronic_illness'] ?? false,
            'disaster_risk_level' => $validated['disaster_risk_level'] ?? null,
            'barangay_program_participation' => $validated['barangay_program_participation'] ?? null,
        ]);

        // Link resident to household as head
        $resident->update(['household_id' => $household->id]);

        return response()->json(['message' => 'Household created successfully!', 'success' => true, 'household_id' => $household->id]);
    }

    public function update(Request $request, Household $household)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (! $resident || $resident->household_id !== $household->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'address_line' => 'required|string|max:255',
            'phase' => 'nullable|integer|min:1',
            'street' => 'nullable|string|max:255', // Added street field
            'contact_no' => 'nullable|string|max:20',
            'household_type' => 'nullable|string|max:50',
            'homeownership_type' => 'nullable|string|max:50',
            'house_type' => 'nullable|string|max:50',
            'has_toilet' => 'nullable|boolean',
            'has_bathroom' => 'nullable|boolean',
            'has_kitchen' => 'nullable|boolean',
            'monthly_income_range' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:50',
            'primary_income_source' => 'nullable|string|max:150',
            'is_4ps_beneficiary' => 'nullable|boolean',
            'is_indigent' => 'nullable|boolean',
            'has_pregnant_member' => 'nullable|boolean',
            'has_senior_citizen' => 'nullable|boolean',
            'has_pwd' => 'nullable|boolean',
            'has_chronic_illness' => 'nullable|boolean',
            'disaster_risk_level' => 'nullable|string|max:50',
            'barangay_program_participation' => 'nullable|string',
        ]);

        $household->update([
            'address_line' => $validated['address_line'],
            'phase' => $validated['phase'],
            'street' => $validated['street'] ?? null,
            'contact_no' => $validated['contact_no'],
            'household_type' => $validated['household_type'],
            'homeownership_type' => $validated['homeownership_type'],
            'house_type' => $validated['house_type'],
            'has_toilet' => $request->boolean('has_toilet'),
            'has_bathroom' => $request->boolean('has_bathroom'),
            'has_kitchen' => $request->boolean('has_kitchen'),
            'has_garage' => $request->boolean('has_garage'),
            'has_electricity' => $request->boolean('has_electricity'),
            'monthly_income_range' => $validated['monthly_income_range'] ?? null,
            'employment_status' => $validated['employment_status'] ?? null,
            'primary_income_source' => $validated['primary_income_source'] ?? null,
            'is_4ps_beneficiary' => $request->boolean('is_4ps_beneficiary'),
            'is_indigent' => $request->boolean('is_indigent'),
            'has_pregnant_member' => $request->boolean('has_pregnant_member'),
            'has_senior_citizen' => $request->boolean('has_senior_citizen'),
            'has_pwd' => $request->boolean('has_pwd'),
            'has_chronic_illness' => $request->boolean('has_chronic_illness'),
            'disaster_risk_level' => $validated['disaster_risk_level'] ?? null,
            'barangay_program_participation' => $validated['barangay_program_participation'] ?? null,
        ]);

        return response()->json(['message' => 'Household updated successfully!', 'success' => true]);
    }

    /**
     * Resident requests to join a household by household_code
     */
    public function requestJoin(Request $request)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (! $resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }

        if ($resident->household_id) {
            return response()->json(['message' => 'You are already a member of a household.'], 400);
        }

        $validated = $request->validate([
            'household_code' => 'required|string|max:50',
            'message' => 'nullable|string|max:500',
        ]);

        // Find household by code
        $household = Household::where('household_code', $validated['household_code'])->first();

        if (! $household) {
            return response()->json(['message' => 'Household code not found.'], 404);
        }

        // Check if already requested
        $existingRequest = HouseholdJoinRequest::where('household_id', $household->id)
            ->where('resident_id', $resident->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json(['message' => 'You already have a pending request for this household.'], 400);
        }

        // Check if already a member
        if ($resident->household_id === $household->id) {
            return response()->json(['message' => 'You are already a member of this household.'], 400);
        }

        try {
            $joinRequest = HouseholdJoinRequest::create([
                'household_id' => $household->id,
                'resident_id' => $resident->id,
                'message' => $validated['message'] ?? null,
                'status' => 'pending',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create request: '.$e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Join request sent successfully! Waiting for household head approval.',
            'success' => true,
            'request' => $joinRequest,
        ]);
    }

    /**
     * Head of household approves a join request
     */
    public function approveRequest(Request $request, HouseholdJoinRequest $joinRequest)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (! $resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }

        // Verify this resident is head of the household
        $household = $joinRequest->household;
        if ($household && $household->head_resident_id !== $resident->id) {
            return response()->json(['message' => 'Only the household head can approve requests.'], 403);
        }

        if ($joinRequest->status !== 'pending') {
            return response()->json(['message' => 'Request has already been processed.'], 400);
        }

        DB::transaction(function () use ($joinRequest) {
            // Add resident to household
            $joinRequest->resident->update(['household_id' => $joinRequest->household_id]);

            // Create household member record
            $joinRequest->household->members()->create([
                'resident_id' => $joinRequest->resident->id,
                'first_name' => $joinRequest->resident->first_name,
                'last_name' => $joinRequest->resident->last_name,
                'email' => $joinRequest->resident->email,
                'relationship' => 'Self',
                'birth_date' => $joinRequest->resident->birth_date,
            ]);

            // Update request status
            $joinRequest->update([
                'status' => 'approved',
                'responded_by' => Auth::id(),
                'responded_at' => now(),
            ]);
        });

        return response()->json(['message' => 'Join request approved. Resident added to household.', 'success' => true]);
    }

    /**
     * Head of household rejects a join request
     */
    public function rejectRequest(Request $request, HouseholdJoinRequest $joinRequest)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (! $resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }

        // Verify this resident is head of the household
        $household = $joinRequest->household;
        if ($household && $household->head_resident_id === $resident->id) {
            return response()->json(['message' => 'Only the household head can reject requests.'], 403);
        }

        if ($joinRequest->status !== 'pending') {
            return response()->json(['message' => 'Request has already been processed.'], 400);
        }

        $joinRequest->update([
            'status' => 'rejected',
            'responded_by' => Auth::id(),
            'responded_at' => now(),
        ]);

        return response()->json(['message' => 'Join request rejected.', 'success' => true]);
    }

    /**
     * Head manually adds a resident by account_no
     */
    public function addMemberByAccount(Request $request)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (! $resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }

        $household = Household::where('head_resident_id', $resident->id)->first();
        if (! $household) {
            return response()->json(['message' => 'Only household head can add members.'], 403);
        }

        $validated = $request->validate([
            'account_no' => 'required|string|exists:residents,account_no',
            'relationship' => 'required|string|max:100',
        ]);

        $memberResident = Resident::where('account_no', $validated['account_no'])->first();

        if (! $memberResident) {
            return response()->json(['message' => 'Resident with that account number not found.'], 404);
        }

        if ($memberResident->household_id) {
            return response()->json(['message' => 'Resident is already a member of a household.'], 400);
        }

        // Add to household
        $memberResident->update(['household_id' => $household->id]);

        // Create household member record
        $household->members()->create([
            'resident_id' => $memberResident->id,
            'first_name' => $memberResident->first_name,
            'last_name' => $memberResident->last_name,
            'email' => $memberResident->email,
            'relationship' => $validated['relationship'],
            'birth_date' => $memberResident->birth_date,
        ]);

        return response()->json(['message' => 'Member added successfully.', 'success' => true]);
    }

    public function togglePWD(Request $request, HouseholdMember $member)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        // Verify the member belongs to the resident's household
        $household = $member->household;
        if (!$household || $household->head_resident_id !== $resident->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $member->update([
            'is_pwd' => $request->boolean('is_pwd', false)
        ]);

        return response()->json(['message' => 'PWD status updated', 'success' => true]);
    }

    public function searchByAccount(Request $request)
    {
        $query = $request->get('q');
        $currentResidentId = $request->get('current_resident_id'); // To exclude current resident if needed

        $residents = Resident::where('account_no', 'like', "%{$query}%")
            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", "%{$query}%")
            ->where('status', 'active')
            ->where(function($q) use ($currentResidentId) {
                if ($currentResidentId) {
                    $q->where('id', '!=', $currentResidentId);
                }
            })
            ->limit(10)
            ->get(['id', 'account_no', 'first_name', 'last_name', 'birth_date', 'email']);

        return response()->json($residents);
    }
}
