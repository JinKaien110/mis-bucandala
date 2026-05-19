<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\Resident;
use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    /**
     * Normalizes an address component for consistent comparison.
     */
    private function normalizeAddressComponent(?string $value): string
    {
        if (!$value) {
            return '';
        }
        $v = mb_strtolower(trim($value));
        // Normalize common abbreviations for address matching
        $v = preg_replace('/\b(block|blk)\b/', 'blk', $v);
        $v = preg_replace('/\b(lot|lt)\b/', 'lt', $v);
        // Remove special characters but keep alphanumeric and spaces
        $v = preg_replace('/[^a-z0-9\s]/', '', $v);
        // Normalize multiple spaces to single space
        $v = preg_replace('/\s+/', ' ', $v);
        return trim($v);
    }
    public function index(Request $request)
    {
        $q = $request->query('q', '');

        $households = Household::query()
            ->notArchived()
            ->with('members.resident') // Eager load for modal displays
            ->withCount('members')
            ->when($q, function ($query) use ($q) {
                // Group where clauses to avoid search conflicts
                $query->where(function ($sub) use ($q) {
                    $sub->where('household_code', 'like', "%{$q}%")
                        ->orWhere('address_line', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        /**
         * For the "Create" part of the index page:
         * We only want residents who are active and don't belong to any household.
         */
        $availableResidents = Resident::where('status', 'active')
            ->whereNull('household_id')
            ->with('user:id,email')
            ->orderBy('last_name')
            ->get(['id', 'user_id', 'first_name', 'last_name', 'birth_date', 'email']);

        return view('admin.households.index', [
            'households' => $households,
            'search' => $q,
            'residents' => $availableResidents,
        ]);
    }

    public function create()
    {
        $residents = Resident::where('status', 'active')
            ->whereNull('household_id')
            ->with('user:id,email')
            ->orderBy('last_name')
            ->get(['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'birth_date', 'household_id', 'email']);

        return view('admin.households.create', compact('residents'));
    }

     // Controller: store
     public function store(Request $request)
     {
         $validated = $request->validate([
             'address_line' => ['required', 'string', 'max:255'],
             'phase' => ['nullable', 'integer', 'min:1'],
             'contact_no' => ['nullable', 'string', 'max:20'],
            'household_type' => ['nullable', 'string', 'max:50'],
            'homeownership_type' => ['nullable', 'string', 'max:50'],
            'total_members' => ['nullable', 'integer', 'min:0'],
            'total_adults' => ['nullable', 'integer', 'min:0'],
            'total_minors' => ['nullable', 'integer', 'min:0'],
            'total_senior_citizens' => ['nullable', 'integer', 'min:0'],
            'total_pwd' => ['nullable', 'integer', 'min:0'],
            'registered_pets_count' => ['nullable', 'integer', 'min:0'],
            'monthly_income_range' => ['nullable', 'string', 'max:50'],
            'employment_status' => ['nullable', 'string', 'max:50'],
            'primary_income_source' => ['nullable', 'string', 'max:150'],
            'house_type' => ['nullable', 'string', 'max:50'],
            'disaster_risk_level' => ['nullable', 'string', 'max:50'],
            'barangay_program_participation' => ['nullable', 'string'],
            'members' => ['required', 'array', 'min:1'],
            'members.*.resident_id' => ['nullable', 'exists:residents,id'],
            'members.*.full_name' => ['required', 'string', 'max:255'],
            'members.*.email' => ['nullable', 'email', 'max:255'],
            'members.*.birth_date' => ['required', 'date'],
            'members.*.relation' => ['required', 'string', 'max:100'],
            'members.*.is_pwd' => ['nullable'],
        ]);

        // Check for duplicate household (normalized address_line + normalized phase combination)
        $normalizedAddressLine = $this->normalizeAddressComponent($validated['address_line']);
        $normalizedPhase = $this->normalizeAddressComponent($validated['phase'] ?? '');

        $existingHousehold = Household::query()
            ->whereRaw('REPLACE(LOWER(TRIM(address_line)), \' \', \'\') = REPLACE(?, \' \', \'\')', [trim($normalizedAddressLine)])
            ->when($normalizedPhase !== '', function ($query) use ($normalizedPhase) {
                $query->whereRaw('REPLACE(LOWER(TRIM(phase)), \' \', \'\') = REPLACE(?, \' \', \'\')', [trim($normalizedPhase)]);
            })
            ->when($normalizedPhase === '', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('phase')
                      ->orWhere('phase', '');
                });
            })
            ->first();

        if ($existingHousehold) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'A household with this address and phase already exists.');
        }

        $year = date('Y'); // current year
        $latest = Household::whereYear('created_at', $year)->latest()->first();

        $nextNumber = 1; // default if no households yet this year
        if ($latest && preg_match('/HH-\d{4}-(\d{6})/', $latest->household_code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        }

        $householdCode = sprintf('HH-%s-%06d', $year, $nextNumber);

         $household = Household::create([
             'household_code' => $householdCode,
             'address_line' => ucfirst(strtolower($validated['address_line'])),
             'phase' => $validated['phase'] ?? null,
             'contact_no' => $validated['contact_no'] ?? null,
            'household_type' => $validated['household_type'] ?? null,
            'homeownership_type' => $validated['homeownership_type'] ?? null,
            'total_members' => $validated['total_members'] ?? 0,
            'total_adults' => $validated['total_adults'] ?? 0,
            'total_minors' => $validated['total_minors'] ?? 0,
            'total_senior_citizens' => $validated['total_senior_citizens'] ?? 0,
            'total_pwd' => $validated['total_pwd'] ?? 0,
            'registered_pets_count' => $validated['registered_pets_count'] ?? 0,
            'monthly_income_range' => $validated['monthly_income_range'] ?? null,
            'employment_status' => $validated['employment_status'] ?? null,
            'primary_income_source' => $validated['primary_income_source'] ?? null,
            'house_type' => $validated['house_type'] ?? null,
            'disaster_risk_level' => $validated['disaster_risk_level'] ?? null,
            'barangay_program_participation' => $validated['barangay_program_participation'] ?? null,
            'has_electricity' => $request->has('has_electricity'),
            'has_toilet' => $request->has('has_toilet'),
            'has_bathroom' => $request->has('has_bathroom'),
            'has_kitchen' => $request->has('has_kitchen'),
            'has_garage' => $request->has('has_garage'),
            'is_4ps_beneficiary' => $request->has('is_4ps_beneficiary'),
            'is_indigent' => $request->has('is_indigent'),
            'has_pregnant_member' => $request->has('has_pregnant_member'),
            'has_senior_citizen' => $request->has('has_senior_citizen'),
            'has_pwd' => $request->has('has_pwd'),
            'has_chronic_illness' => $request->has('has_chronic_illness'),
        ]);

        foreach ($validated['members'] as $member) {
            // Split full name if no resident selected
            $first = $last = '';
            if (! empty($member['resident_id'])) {
                $resident = Resident::find($member['resident_id']);
                $first = $resident->first_name;
                $last = $resident->last_name;
                $resident->household_id = $household->id;
                $resident->save();
            } else {
                $parts = explode(' ', $member['full_name'], 2);
                $first = $parts[0];
                $last = $parts[1] ?? '';
            }

            $household->members()->create([
                'resident_id' => $member['resident_id'] ?? null,
                'first_name' => $first,
                'last_name' => $last,
                'email' => $member['email'] ?? null,
                'birth_date' => $member['birth_date'],
                'relationship' => $member['relation'],
                'is_pwd' => isset($member['is_pwd']) && $member['is_pwd'] == '1',
            ]);
        }

        return redirect()->route('admin.households.index')
            ->with('success', 'Household created successfully.');
    }

    public function show(Household $household)
    {
        // Eager load members and their associated resident profiles
        $household->load(['members.resident']);

        // Get residents for the "Add Member" dropdown/list
        $availableResidents = Resident::where('status', 'active')
            ->where(function ($query) use ($household) {
                $query->whereNull('household_id')
                    ->orWhere('household_id', $household->id);
            })
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'birth_date']);

        // Head of household logic
        $headResident = $household->members()
    ->where('relationship', 'Head')
    ->with('resident')
    ->first()?->resident;

        return view('admin.households.show', compact('household', 'availableResidents', 'headResident'));
    }

    public function edit(Household $household)
    {
        // Get residents not assigned to any household or are members of this household
        $availableResidents = Resident::where('status', 'active')
            ->where(function ($query) use ($household) {
                $query->whereNull('household_id')
                    ->orWhere('household_id', $household->id);
            })
            ->with('user:id,email')
            ->orderBy('last_name')
            ->get(['id', 'user_id', 'first_name', 'middle_name', 'last_name', 'birth_date', 'email']);

        $household->load('members.resident');

        return view('admin.households.edit', compact('household', 'availableResidents'));
    }

   public function update(Request $request, Household $household)
{
    $validated = $request->validate([
        'address_line' => ['required','string','max:255'],
        'phase' => ['nullable','integer','min:1'],
        'contact_no' => ['nullable','string','max:20'],
        'household_type' => ['nullable','string','max:50'],
        'homeownership_type' => ['nullable','string','max:50'],
        'house_type' => ['nullable','string','max:50'],
        'disaster_risk_level' => ['nullable','string','max:50'],
        'barangay_program_participation' => ['nullable','string'],
        'monthly_income_range' => ['nullable','string','max:50'],
        'employment_status' => ['nullable','string','max:50'],
        'primary_income_source' => ['nullable','string','max:150'],
        'registered_pets_count' => ['nullable','integer','min:0'],

        'members' => ['required','array','min:1'],

        // household_members row id (hidden input for existing rows)
        'members.*.id' => ['nullable','exists:household_members,id'],

        'members.*.resident_id' => ['nullable','exists:residents,id'],
        'members.*.full_name' => ['required','string','max:255'],
        'members.*.email' => ['nullable','email','max:255'],
        'members.*.birth_date' => ['required','date'],
        'members.*.relation' => ['required','string','max:100'],
        'members.*.is_pwd' => ['nullable'],
    ]);


    DB::transaction(function () use ($validated, $request, $household) {

        /*
        |--------------------------------------------------------------------------
        | Update Household
        |--------------------------------------------------------------------------
        */

        $household->update([
            'address_line' => ucfirst(strtolower($validated['address_line'])),
            'phase' => $validated['phase'] ?? null,
            'contact_no' => $validated['contact_no'] ?? null,

            'household_type' => $validated['household_type'] ?? null,
            'homeownership_type' => $validated['homeownership_type'] ?? null,
            'house_type' => $validated['house_type'] ?? null,

            'disaster_risk_level' =>
                $validated['disaster_risk_level'] ?? null,

            'barangay_program_participation' =>
                $validated['barangay_program_participation'] ?? null,

            'monthly_income_range' =>
                $validated['monthly_income_range'] ?? null,

            'employment_status' =>
                $validated['employment_status'] ?? null,

            'primary_income_source' =>
                $validated['primary_income_source'] ?? null,

            'registered_pets_count' =>
                $validated['registered_pets_count'] ?? 0,

            'has_electricity' => $request->has('has_electricity'),
            'has_toilet' => $request->has('has_toilet'),
            'has_bathroom' => $request->has('has_bathroom'),
            'has_kitchen' => $request->has('has_kitchen'),
            'has_garage' => $request->has('has_garage'),

            'is_4ps_beneficiary' => $request->has('is_4ps_beneficiary'),
            'is_indigent' => $request->has('is_indigent'),

            'has_pregnant_member' =>
                $request->has('has_pregnant_member'),

            'has_senior_citizen' =>
                $request->has('has_senior_citizen'),

            'has_pwd' => $request->has('has_pwd'),

            'has_chronic_illness' =>
                $request->has('has_chronic_illness'),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Sync Members
        |--------------------------------------------------------------------------
        */

        $submittedMemberIds = [];
        $submittedResidentIds = [];

        foreach ($validated['members'] as $member) {

            $householdMemberId = $member['id'] ?? null;

            $first = '';
            $last = '';
            $email = $member['email'] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Existing resident-linked member
            |--------------------------------------------------------------------------
            */

            if (!empty($member['resident_id'])) {

                $resident = Resident::find($member['resident_id']);

                if ($resident) {

                    $resident->update([
                        'household_id' => $household->id,
                        'birth_date' => $member['birth_date'],
                    ]);

                    $submittedResidentIds[] = $resident->id;

                    $first = $resident->first_name;
                    $last = $resident->last_name;

                    $email =
                        $resident->user?->email
                        ?: $email;
                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | Manual member (non-resident)
                |--------------------------------------------------------------------------
                */

                $parts = explode(' ', $member['full_name'], 2);

                $first = $parts[0];
                $last = $parts[1] ?? '';
            }


            /*
            |--------------------------------------------------------------------------
            | Update existing household member
            |--------------------------------------------------------------------------
            */

            if ($householdMemberId) {

                $existingMember = $household->members()
                    ->where('id', $householdMemberId)
                    ->first();

                if ($existingMember) {

                    $existingMember->update([
                        'resident_id' =>
                            $member['resident_id'] ?? null,

                        'first_name' => $first,
                        'last_name' => $last,
                        'email' => $email,

                        'birth_date' =>
                            $member['birth_date'],

                        'relationship' =>
                            $member['relation'],

                        'is_pwd' =>
                            !empty($member['is_pwd']),
                    ]);

                    $submittedMemberIds[] =
                        $existingMember->id;
                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | Create brand new member
                |--------------------------------------------------------------------------
                */

                $newMember = $household->members()->create([
                    'resident_id' =>
                        $member['resident_id'] ?? null,

                    'first_name' => $first,
                    'last_name' => $last,
                    'email' => $email,

                    'birth_date' =>
                        $member['birth_date'],

                    'relationship' =>
                        $member['relation'],

                    'is_pwd' =>
                        !empty($member['is_pwd']),
                ]);

                $submittedMemberIds[] = $newMember->id;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Remove deleted household members
        |--------------------------------------------------------------------------
        */

        $removedResidentIds = $household->members()
            ->whereNotIn('id', $submittedMemberIds)
            ->whereNotNull('resident_id')
            ->pluck('resident_id');

        if ($removedResidentIds->isNotEmpty()) {
            Resident::whereIn('id', $removedResidentIds)
                ->update([
                    'household_id' => null
                ]);
        }

        $household->members()
            ->whereNotIn('id', $submittedMemberIds)
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | Recalculate stats
        |--------------------------------------------------------------------------
        */

        $this->recalculateHouseholdStats($household);

    });


    return redirect()
        ->route('admin.households.show', $household)
        ->with(
            'success',
            'Household updated successfully.'
        );
}

    public function destroy(Household $household)
    {
        $household->archive();

        return redirect()->route('admin.households.index')
            ->with('success', 'Household archived successfully.');
    }

    public function restore(Household $household)
    {
        $household->restore();

        return redirect()->route('admin.households.index')
            ->with('success', 'Household restored successfully.');
    }

    public function addMember(Request $request, Household $household)
    {
        $data = $request->validate([
            'resident_id' => ['required', 'exists:residents,id'],
            'relationship' => ['required', 'string', 'max:100'],
        ]);

        $resident = Resident::findOrFail($data['resident_id']);
        $resident->update([
            'household_id' => $household->id,
        ]);

        return back()->with('success', 'Member added to household.');
    }

    public function removeMember(Household $household, Resident $resident)
    {
        if ($resident->household_id === $household->id) {
            $resident->update(['household_id' => null]);

            return back()->with('success', 'Member removed from household.');
        }

        return back()->with('error', 'Resident is not a member of this household.');
    }
}
