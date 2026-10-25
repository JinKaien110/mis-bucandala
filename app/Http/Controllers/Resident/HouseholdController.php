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
use Illuminate\Validation\ValidationException;

class HouseholdController extends Controller
{
    private const HOUSEHOLD_INVITE_PREFIX = '[HOUSEHOLD-INVITE]';

    private function isHouseholdInvite(HouseholdJoinRequest $joinRequest): bool
    {
        return str_starts_with((string) $joinRequest->message, self::HOUSEHOLD_INVITE_PREFIX);
    }

    private function invitedRelationship(HouseholdJoinRequest $joinRequest): string
    {
        $relationship = trim(substr((string) $joinRequest->message, strlen(self::HOUSEHOLD_INVITE_PREFIX)));
        $allowed = ['Member', 'Spouse', 'Child', 'Parent', 'Sibling', 'Grandparent', 'Grandchild', 'Relative', 'Other'];

        return in_array($relationship, $allowed, true) ? $relationship : 'Member';
    }

    public function index()
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        $household = null;
        $members = [];
        $joinRequests = collect();
        $householdInvitations = collect();
        $pendingRequestsCount = 0;

        if ($resident && $resident->household_id) {
            $household = Household::with(['members.resident.user'])->find($resident->household_id);
            if ($household) {
                $isHouseholdMember = $household->members()
                    ->where('resident_id', $resident->id)
                    ->exists();

                if ($isHouseholdMember) {
                    $joinRequests = HouseholdJoinRequest::where('household_id', $household->id)
                        ->where('status', 'pending')
                        ->where(function ($query) {
                            $query->whereNull('message')
                                ->orWhere('message', 'not like', self::HOUSEHOLD_INVITE_PREFIX . '%');
                        })
                        ->with(['resident', 'responder'])
                        ->latest()
                        ->get();
                    $pendingRequestsCount = $joinRequests->count();
                }
            }
        }

        if ($resident) {
            $householdInvitations = HouseholdJoinRequest::where('resident_id', $resident->id)
                ->where('status', 'pending')
                ->where('message', 'like', self::HOUSEHOLD_INVITE_PREFIX . '%')
                ->with('household')
                ->latest()
                ->get();
        }

        return view('resident.household', compact('user', 'resident', 'household', 'joinRequests', 'pendingRequestsCount', 'householdInvitations'));
    }

    private function serializeBarangayProgramParticipation(Request $request, array $validated): ?string
    {
        // Keep DB column as TEXT. Serialize selected checkboxes + optional "other" as comma-separated string.
        $selected = $request->input('barangay_program_participation', []);
        if (!is_array($selected)) {
            $selected = [$selected];
        }

        $selected = array_values(array_filter(array_map(function ($v) {
            return is_string($v) ? trim($v) : '';
        }, $selected), function ($v) {
            return $v !== '';
        }));

        $other = isset($validated['barangay_program_participation_other']) ? trim((string) $validated['barangay_program_participation_other']) : '';
        if ($other !== '') {
            $selected[] = $other;
        }

        if (count($selected) === 0) {
            return null;
        }

        // De-duplicate but keep order.
        $seen = [];
        $unique = [];
        foreach ($selected as $item) {
            $key = mb_strtolower($item);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $item;
            }
        }

        return implode(', ', $unique);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (! $resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }
        if ($resident->household_id) {
            return response()->json(['message' => 'You already belong to a household.'], 409);
        }

        $validated = $request->validate([
            'phase' => 'nullable|in:A,B,C',
            'contact_no' => 'nullable|string|max:20',
            'household_type' => 'nullable|string|max:50',
            'homeownership_type' => 'nullable|string|max:50',
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

            // Updated to accept checkbox array
            'barangay_program_participation' => 'nullable|array',
            'barangay_program_participation.*' => 'nullable|string|max:255',
            'barangay_program_participation_other' => 'nullable|string|max:255',
            'household_members' => ['nullable', 'array', 'max:20'],
        ]);

        $validated['household_members'] = [];
        foreach ($request->input('household_members', []) as $index => $memberData) {
            if (! is_array($memberData)) {
                throw ValidationException::withMessages([
                    "household_members.{$index}" => 'Each household member must be a valid entry.',
                ]);
            }

            $memberRules = ($memberData['type'] ?? null) === 'account'
                ? [
                    'account_no' => ['required', 'string', 'max:255', 'exists:residents,account_no'],
                ]
                : [
                    'first_name' => ['required', 'string', 'max:255'],
                    'last_name' => ['required', 'string', 'max:255'],
                    'email' => ['nullable', 'email', 'max:255'],
                    'birth_date' => ['required', 'date', 'before_or_equal:today'],
                    'is_pwd' => ['nullable', 'boolean'],
                    'is_4ps_beneficiary' => ['nullable', 'boolean'],
                    'is_indigent' => ['nullable', 'boolean'],
                    'has_pregnant_member' => ['nullable', 'boolean'],
                    'has_senior_citizen' => ['nullable', 'boolean'],
                    'has_chronic_illness' => ['nullable', 'boolean'],
                ];

            $validatedMemberData = validator($memberData, array_merge([
                'type' => ['required', 'in:account,manual'],
                'relationship' => ['required', 'in:Member,Spouse,Child,Parent,Sibling,Grandparent,Grandchild,Relative,Other'],
            ], $memberRules))->validate();
            $validated['household_members'][$index] = $validatedMemberData;
        }

        $barangayParticipationSerialized = $this->serializeBarangayProgramParticipation($request, $validated);

        $household = DB::transaction(function () use ($validated, $request, $resident, $user, $barangayParticipationSerialized) {
            $year = now()->year;
            $latest = Household::whereYear('created_at', $year)
                ->lockForUpdate()
                ->latest()
                ->first();
            $nextNumber = 1;
            if ($latest && preg_match('/HH-\d{4}-(\d{7})/', $latest->household_code, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            }

            $household = Household::create([
                'household_code' => sprintf('HH-%s-%07d', $year, $nextNumber),
                'address_line' => $resident->address_line,
                'phase' => $resident->phase,
                'street' => $resident->street ?? null,
                'contact_no' => $resident->contact_no,
                'homeownership_type' => $validated['homeownership_type'] ?? null,
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
                'barangay_program_participation' => $barangayParticipationSerialized,
            ]);

            $household->members()->create([
                'resident_id' => $resident->id,
                'first_name' => $resident->first_name,
                'last_name' => $resident->last_name,
                'email' => $user->email,
                'birth_date' => $resident->birth_date,
                'relationship' => 'Head',
                'is_pwd' => $resident->pwd ? 1 : 0,
                'is_4ps_beneficiary' => $resident->four_ps_beneficiary ? 1 : 0,
                'is_indigent' => $resident->indigent ? 1 : 0,
                'has_pregnant_member' => 0,
                'has_senior_citizen' => 0,
                'has_chronic_illness' => 0,
            ]);

            $invitedResidentIds = [];
            foreach ($validated['household_members'] ?? [] as $memberData) {
                if ($memberData['type'] === 'account') {
                    $memberResident = Resident::where('account_no', $memberData['account_no'])
                        ->lockForUpdate()
                        ->first();

                    if (! $memberResident || $memberResident->id === $resident->id ||
                        $memberResident->household_id || in_array($memberResident->id, $invitedResidentIds, true)) {
                        throw ValidationException::withMessages([
                            'household_members' => 'Each registered member must be a different resident who is not already in a household.',
                        ]);
                    }

                    $invitedResidentIds[] = $memberResident->id;
                    HouseholdJoinRequest::create([
                        'household_id' => $household->id,
                        'resident_id' => $memberResident->id,
                        'status' => 'pending',
                        'message' => self::HOUSEHOLD_INVITE_PREFIX . $memberData['relationship'],
                    ]);
                    continue;
                }

                $household->members()->create([
                    'resident_id' => null,
                    'first_name' => $memberData['first_name'],
                    'last_name' => $memberData['last_name'],
                    'email' => $memberData['email'] ?? '',
                    'birth_date' => $memberData['birth_date'],
                    'relationship' => $memberData['relationship'],
                    'is_pwd' => (bool) ($memberData['is_pwd'] ?? false),
                    'is_4ps_beneficiary' => (bool) ($memberData['is_4ps_beneficiary'] ?? false),
                    'is_indigent' => (bool) ($memberData['is_indigent'] ?? false),
                    'has_pregnant_member' => (bool) ($memberData['has_pregnant_member'] ?? false),
                    'has_senior_citizen' => (bool) ($memberData['has_senior_citizen'] ?? false),
                    'has_chronic_illness' => (bool) ($memberData['has_chronic_illness'] ?? false),
                ]);
            }

            $resident->update(['household_id' => $household->id]);
            return $household;
        });

        $invitationCount = collect($validated['household_members'] ?? [])
            ->where('type', 'account')
            ->count();
        $message = $invitationCount > 0
            ? 'Household created successfully. Invitation sent to registered resident(s); they must accept before joining.'
            : 'Household created successfully!';

        return response()->json(['message' => $message, 'success' => true, 'household_id' => $household->id]);
    }

    public function update(Request $request, Household $household)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (! $resident || $resident->household_id !== $household->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if (! $household->members()->where('resident_id', $resident->id)->exists()) {
            return response()->json(['message' => 'Only household members can update household information.'], 403);
        }

        $validated = $request->validate([
            'address_line' => 'required|string|max:255',
            'phase' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'household_type' => 'nullable|string|max:50',
            'homeownership_type' => 'nullable|string|max:50',
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

            // Updated to accept checkbox array
            'barangay_program_participation' => 'nullable|array',
            'barangay_program_participation.*' => 'nullable|string|max:255',
            'barangay_program_participation_other' => 'nullable|string|max:255',
        ]);

        $barangayParticipationSerialized = $this->serializeBarangayProgramParticipation($request, $validated);

        $household->update([
            'address_line' => $validated['address_line'],
            'phase' => $validated['phase'] ?? null,
            'street' => $validated['street'] ?? null,
            'contact_no' => $validated['contact_no'] ?? null,
            'homeownership_type' => $validated['homeownership_type'] ?? null,
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
            'barangay_program_participation' => $barangayParticipationSerialized,
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
        if (str_starts_with((string) ($validated['message'] ?? ''), self::HOUSEHOLD_INVITE_PREFIX)) {
            return response()->json(['message' => 'This invitation marker is reserved.'], 422);
        }

        // Find household by code
        $household = Household::where('household_code', $validated['household_code'])->first();

        if (! $household) {
            return response()->json(['message' => 'Household code not found.'], 404);
        }

        $existingRequest = HouseholdJoinRequest::where('household_id', $household->id)
            ->where('resident_id', $resident->id)
            ->first();

        if ($existingRequest?->status === 'pending') {
            return response()->json(['message' => 'You already have a pending request for this household.'], 400);
        }

        // Check if already a member
        if ($resident->household_id === $household->id) {
            return response()->json(['message' => 'You are already a member of this household.'], 400);
        }

        if ($existingRequest) {
            $existingRequest->update([
                'message' => $validated['message'] ?? null,
                'status' => 'pending',
                'responded_by' => null,
                'responded_at' => null,
            ]);
            $joinRequest = $existingRequest->fresh();
        } else {
            $joinRequest = HouseholdJoinRequest::create([
                'household_id' => $household->id,
                'resident_id' => $resident->id,
                'message' => $validated['message'] ?? null,
                'status' => 'pending',
            ]);
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

        $household = $joinRequest->household;
        if ($this->isHouseholdInvite($joinRequest)) {
            if ((int) $joinRequest->resident_id !== (int) $resident->id) {
                return response()->json(['message' => 'Only the invited resident can accept this invitation.'], 403);
            }
            if ($joinRequest->status !== 'pending') {
                return response()->json(['message' => 'Invitation has already been processed.'], 400);
            }
            if (! $household || $resident->household_id) {
                return response()->json(['message' => 'You must not already belong to another household to accept this invitation.'], 409);
            }

            DB::transaction(function () use ($joinRequest, $resident, $household) {
                $lockedRequest = HouseholdJoinRequest::whereKey($joinRequest->id)->lockForUpdate()->firstOrFail();
                $lockedResident = Resident::whereKey($resident->id)->lockForUpdate()->firstOrFail();
                if ($lockedRequest->status !== 'pending' || $lockedResident->household_id) {
                    throw ValidationException::withMessages([
                        'invitation' => 'This invitation is no longer available or you already belong to a household.',
                    ]);
                }

                $lockedResident->update(['household_id' => $household->id]);
                $household->members()->create([
                    'resident_id' => $lockedResident->id,
                    'first_name' => $lockedResident->first_name,
                    'last_name' => $lockedResident->last_name,
                    'email' => $lockedResident->user?->email ?? $lockedResident->email ?? '',
                    'birth_date' => $lockedResident->birth_date,
                    'relationship' => $this->invitedRelationship($lockedRequest),
                    'is_pwd' => $lockedResident->pwd ? 1 : 0,
                    'is_4ps_beneficiary' => $lockedResident->four_ps_beneficiary ? 1 : 0,
                    'is_indigent' => $lockedResident->indigent ? 1 : 0,
                ]);
                $lockedRequest->update([
                    'status' => 'approved',
                    'responded_by' => Auth::id(),
                    'responded_at' => now(),
                ]);
            });

            return response()->json(['message' => 'Household invitation accepted. You have joined the household.', 'success' => true]);
        }

        if (! $household || $resident->household_id !== $household->id ||
            ! $household->members()->where('resident_id', $resident->id)->exists()) {
            return response()->json(['message' => 'Only household members can approve join requests.'], 403);
        }

        if ($joinRequest->status !== 'pending') {
            return response()->json(['message' => 'Request has already been processed.'], 400);
        }

        DB::transaction(function () use ($joinRequest) {
            // Add resident to household
            $joinRequest->resident->update(['household_id' => $joinRequest->household_id]);

            // Create household member record
            // Relationship for joined members should be something other than 'Head'
            $joinRequest->household->members()->create([
                'resident_id' => $joinRequest->resident->id,
                'first_name' => $joinRequest->resident->first_name,
                'last_name' => $joinRequest->resident->last_name,
                'email' => $joinRequest->resident->email,
                'relationship' => 'Member',
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

        $household = $joinRequest->household;
        if ($this->isHouseholdInvite($joinRequest)) {
            if ((int) $joinRequest->resident_id !== (int) $resident->id) {
                return response()->json(['message' => 'Only the invited resident can reject this invitation.'], 403);
            }
            if ($joinRequest->status !== 'pending') {
                return response()->json(['message' => 'Invitation has already been processed.'], 400);
            }

            $joinRequest->update([
                'status' => 'rejected',
                'responded_by' => Auth::id(),
                'responded_at' => now(),
            ]);

            return response()->json(['message' => 'Household invitation rejected.', 'success' => true]);
        }

        if (! $household || $resident->household_id !== $household->id ||
            ! $household->members()->where('resident_id', $resident->id)->exists()) {
            return response()->json(['message' => 'Only household members can reject join requests.'], 403);
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
     * A household member adds a resident by account number.
     */
    public function addMemberByAccount(Request $request)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (! $resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }

        $household = $resident->household_id ? Household::find($resident->household_id) : null;

        if (! $household || ! $household->members()->where('resident_id', $resident->id)->exists()) {
            return response()->json(['message' => 'Only household members can add members.'], 403);
        }

        $validated = $request->validate([
            'account_no' => ['required', 'string', 'exists:residents,account_no'],
            'relationship' => ['required', 'in:Member,Spouse,Child,Parent,Sibling,Grandparent,Grandchild,Relative,Other'],
        ]);

        $memberResident = Resident::where('account_no', $validated['account_no'])->first();

        if (! $memberResident) {
            return response()->json(['message' => 'Resident with that account number not found.'], 404);
        }
        if ($memberResident->id === $resident->id) {
            return response()->json(['message' => 'You are already a member of this household.'], 422);
        }

        if ($memberResident->household_id) {
            return response()->json(['message' => 'Resident is already a member of a household.'], 400);
        }

        $existingRequest = HouseholdJoinRequest::where('household_id', $household->id)
            ->where('resident_id', $memberResident->id)
            ->first();
        if ($existingRequest) {
            if ($existingRequest->status === 'pending') {
                return response()->json(['message' => 'An invitation is already pending for this resident.'], 409);
            }
            $existingRequest->update([
                'status' => 'pending',
                'message' => self::HOUSEHOLD_INVITE_PREFIX . $validated['relationship'],
                'responded_by' => null,
                'responded_at' => null,
            ]);
        } else {
            HouseholdJoinRequest::create([
                'household_id' => $household->id,
                'resident_id' => $memberResident->id,
                'status' => 'pending',
                'message' => self::HOUSEHOLD_INVITE_PREFIX . $validated['relationship'],
            ]);
        }

        return response()->json(['message' => 'Household invitation sent. The resident must accept before being added.', 'success' => true]);
    }

    public function addMemberManually(Request $request)
    {
        $resident = Resident::where('user_id', Auth::id())->first();
        $household = $resident?->household_id
            ? Household::find($resident->household_id)
            : null;

        if (! $household || ! $household->members()->where('resident_id', $resident->id)->exists()) {
            return response()->json(['message' => 'Only household members can add members.'], 403);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'relationship' => ['required', 'in:Member,Spouse,Child,Parent,Sibling,Grandparent,Grandchild,Relative,Other'],
            'is_pwd' => ['nullable', 'boolean'],
            'is_4ps_beneficiary' => ['nullable', 'boolean'],
            'is_indigent' => ['nullable', 'boolean'],
            'has_pregnant_member' => ['nullable', 'boolean'],
            'has_senior_citizen' => ['nullable', 'boolean'],
            'has_chronic_illness' => ['nullable', 'boolean'],
        ]);

        $household->members()->create([
            'resident_id' => null,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            // The current household_members migration makes email non-nullable.
            'email' => $validated['email'] ?? '',
            'birth_date' => $validated['birth_date'],
            'relationship' => $validated['relationship'],
            'is_pwd' => $request->boolean('is_pwd'),
            'is_4ps_beneficiary' => $request->boolean('is_4ps_beneficiary'),
            'is_indigent' => $request->boolean('is_indigent'),
            'has_pregnant_member' => $request->boolean('has_pregnant_member'),
            'has_senior_citizen' => $request->boolean('has_senior_citizen'),
            'has_chronic_illness' => $request->boolean('has_chronic_illness'),
        ]);

        return response()->json(['message' => 'Household member added successfully.', 'success' => true]);
    }

    /**
     * Household members can update relationships while preserving one household head.
     */
    public function bulkUpdateMembers(Request $request)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (!$resident || !$resident->household_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $household = Household::find($resident->household_id);
        if (! $household) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $isHouseholdMember = $household->members()
            ->where('resident_id', $resident->id)
            ->exists();
        if (! $isHouseholdMember) {
            return response()->json(['message' => 'Only household members can update relationships.'], 403);
        }

        $validated = $request->validate([
            'members' => ['required', 'array', 'min:1'],
            'members.*.id' => ['required', 'integer', 'distinct', \Illuminate\Validation\Rule::exists('household_members', 'id')->where('household_id', $household->id)],
            'members.*.relationship' => ['required', 'in:Head,Member,Spouse,Child,Parent,Sibling,Grandparent,Grandchild,Relative,Other'],
        ]);

        $existingMembers = $household->members()->get(['id', 'resident_id', 'relationship']);
        $submittedMemberIds = collect($validated['members'])->pluck('id')->map(fn ($id) => (int) $id)->sort()->values();
        $existingMemberIds = $existingMembers->pluck('id')->map(fn ($id) => (int) $id)->sort()->values();

        if ($submittedMemberIds->all() !== $existingMemberIds->all()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'members' => 'Submit each household member exactly once.',
            ]);
        }

        $headCount = collect($validated['members'])
            ->where('relationship', 'Head')
            ->count();
        if ($headCount !== 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'members' => 'A household must have exactly one Head.',
            ]);
        }

        DB::transaction(function () use ($household, $validated) {
            $membersById = $household->members()->lockForUpdate()->get()->keyBy('id');
            foreach ($validated['members'] as $memberData) {
                $membersById[(int) $memberData['id']]->update([
                    'relationship' => $memberData['relationship'],
                ]);
            }
        });

        return response()->json(['message' => 'Household members updated successfully!', 'success' => true]);
    }

    /**
     * Household members update a member relationship through the bulk editor.
     */
    public function updateMember(Request $request, HouseholdMember $member)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        if (!$resident) {
            return response()->json(['message' => 'Resident profile not found.'], 404);
        }

        $household = $member->household;
        if (!$household || $resident->household_id !== $household->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if (! $household->members()->where('resident_id', $resident->id)->exists()) {
            return response()->json(['message' => 'Only household members can update household relationships.'], 403);
        }

        $validated = $request->validate([
            'relationship' => 'required|in:Head,Member,Spouse,Child,Parent,Sibling,Grandparent,Grandchild,Relative,Other',
        ]);

        if ($member->relationship === 'Head' && $validated['relationship'] !== 'Head') {
            return response()->json(['message' => 'Use Edit Members to transfer the Head relationship and select the new Head.'], 422);
        }

        DB::transaction(function () use ($household, $member, $validated) {
            if ($validated['relationship'] === 'Head') {
                $household->members()
                    ->where('id', '!=', $member->id)
                    ->where('relationship', 'Head')
                    ->update(['relationship' => 'Member']);
            }

            $member->update(['relationship' => $validated['relationship']]);
        });

        return response()->json(['message' => 'Member updated successfully!', 'success' => true]);
    }

    public function togglePWD(Request $request, HouseholdMember $member)
    {
        $user = Auth::user();
        $resident = Resident::where('user_id', $user->id)->first();

        $household = $member->household;
        if (! $resident || ! $household ||
            $resident->household_id !== $household->id ||
            ! $household->members()->where('resident_id', $resident->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $member->update([
            'is_pwd' => $request->boolean('is_pwd', false)
        ]);

        return response()->json(['message' => 'PWD status updated', 'success' => true]);
    }

    public function removeMember(HouseholdMember $member)
    {
        $resident = Resident::where('user_id', Auth::id())->first();
        $household = $member->household;

        if (! $resident || ! $household || $resident->household_id !== $household->id ||
            ! $household->members()->where('resident_id', $resident->id)->exists()) {
            return response()->json(['message' => 'Only household members can remove household members.'], 403);
        }

        if ($member->relationship === 'Head') {
            return response()->json(['message' => 'Transfer the Head relationship before removing this member.'], 422);
        }
        if ((int) $member->resident_id === (int) $resident->id) {
            return response()->json(['message' => 'You cannot remove your own household membership.'], 422);
        }

        DB::transaction(function () use ($member) {
            if ($member->resident_id) {
                Resident::whereKey($member->resident_id)
                    ->where('household_id', $member->household_id)
                    ->update(['household_id' => null]);
            }
            $member->delete();
        });

        return response()->json(['message' => 'Household member removed successfully.', 'success' => true]);
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
