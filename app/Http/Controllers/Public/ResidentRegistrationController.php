<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\Household;
use App\Models\OtpVerification;
use App\Models\Resident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ResidentRegistrationController extends Controller
{
    /* -------------------------------------------------------------------------
     *  Address normalization helpers
     * -------------------------------------------------------------------------
     */
    private function normalize(string $value): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $value)));
    }

    private function normalizeAddressComponent(?string $value, string $type='address_line'): string
    {
        if (!$value) {
            return '';
        }

        $v = mb_strtolower(trim($value));

        if($type === "phase") {
             if (preg_match('/\d+/', $v, $m)) {
                return $m[0];
            }

            return '';
        }
        
            // 1. Normalize word boundaries first (IMPORTANT for Blk2Lot5 cases)
            $v = preg_replace('/([a-z])([0-9])/', '$1 $2', $v);
            $v = preg_replace('/([0-9])([a-z])/', '$1 $2', $v);

            // 2. Normalize common abbreviations
            $v = preg_replace('/\b(block|blk)\b/', 'blk', $v);
            $v = preg_replace('/\b(lot|lt)\b/', 'lt', $v);

            // 3. Remove punctuation/special characters (keep letters, numbers, spaces)
            $v = preg_replace('/[^a-z0-9\s]/', '', $v);

            // 4. Normalize multiple spaces
            $v = preg_replace('/\s+/', ' ', $v);    

            return trim($v);

    }

    /**
     * Find existing household by matching normalized address_line and (optionally) phase.
     * Returns Household or null.
     */
    private function findExistingHousehold(string $addressLine, ?string $phase)
    {
        $addrNorm  = $this->normalizeAddressComponent($addressLine, "address_line");
        $phaseNorm = $this->normalizeAddressComponent($phase, "phase");

        $households = Household::all();

        foreach ($households as $household) {

            $dbAddrNorm  = $this->normalizeAddressComponent($household->address_line, 'address_line');
            $dbPhaseNorm = $this->normalizeAddressComponent($household->phase, 'phase');

            if ($dbAddrNorm === $addrNorm && $dbPhaseNorm === $phaseNorm) {
                return $household;
            }
        }

        return null;
    }

    /**
     * Create a new household with auto-generated code.
     */
    private function createHousehold(string $addressLine, ?string $phase, ?string $contactNo = null): Household
    {
        return DB::transaction(function () use ($addressLine, $phase, $contactNo) {
            $year = now()->year;

            $last = Household::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $next = 1;
            if ($last && preg_match('/^HH-'.$year.'-(\d{6})$/', $last->household_code, $m)) {
                $next = ((int) $m[1]) + 1;
            }

            return Household::create([
                'household_code' => 'HH-'.$year.'-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT),
                'address_line'   => $addressLine,
                'street'         => null,
                'phase'          => $phase,
                'contact_no'     => $contactNo,
                'total_members'         => 0,
                'total_adults'          => 0,
                'total_minors'          => 0,
                'total_senior_citizens' => 0,
                'total_pwd'             => 0,
                'registered_pets_count' => 0,
            ]);
        });
    }

    /**
     * Attach resident to household.
     * Sets relationship:
     * - Head if this household has no members yet
     * - Member otherwise
     * Also ensures the household head FK is set automatically when the first member is added.
     */
    private function attachResidentToHousehold(Household $household, Resident $resident): void
    {
        $count = $household->members()->count();

        // Per requirement: when a resident creates a household, that resident should become the Head member.
        // However, this controller registers multiple residents when creating a household in bulk.
        // We therefore always set the relationship based on whether this is the *first* member row being added.
        // Head identification in UI should rely on HouseholdMember.relationship (not household.head_resident_id).
        $relationship = ($count === 0) ? 'Head' : 'Member';

        // Do NOT set/modify $household->head_resident_id here.
        // Head should be derived from HouseholdMember.relationship.

        $household->members()->create([
            'resident_id'  => $resident->id,
            'first_name'   => $resident->first_name,
            'last_name'    => $resident->last_name,
            'email'        => $resident->user?->email ?? null,
            'birth_date'   => $resident->birth_date,
            'relationship' => $relationship,
            'is_pwd'       => false,
        ]);
    }


    /**
     * Assigns a resident to an existing household, creates a new one, or leaves household_id null.
     * Returns the assigned Household instance or null.
     * This method is called *after* the resident record is created.
     */
    private function assignResidentToAppropriateHousehold(
        Resident $resident,
        string $addressLine,
        ?string $phase
    ): ?Household
    {
        // STEP 1: try existing household match
        $household = $this->findExistingHousehold($addressLine, $phase);
        if ($household) {

            $resident->household_id = $household->id;
            $resident->save();

            $this->attachResidentToHousehold($household, $resident);
            $this->recalculateHouseholdStats($household);

            return $household;
        }

        // STEP 2: find other residents with same normalized address + phase
         $matchingResidents = Resident::whereNull('household_id')
        ->where('id', '!=', $resident->id)
        ->get()
        ->filter(function ($r) use ($addressLine, $phase) {

            return
                $this->normalizeAddressComponent($r->address_line, 'address_line') ===
                $this->normalizeAddressComponent($addressLine, 'address_line')
                &&
                $this->normalizeAddressComponent($r->phase, 'phase') ===
                $this->normalizeAddressComponent($phase, 'phase');
        });

        // must have at least 1 OTHER resident
        if ($matchingResidents->isNotEmpty()) {
            // include current resident FIRST, so they become the Head member ('relationship' = 'Head')


            $household = $this->createHousehold($addressLine, $phase);

            $allResidents = collect([$resident])->merge($matchingResidents->values());

            foreach ($allResidents as $r) {

                $r->household_id = $household->id;
                $r->save();

                $this->attachResidentToHousehold($household, $r);
            }

            $this->recalculateHouseholdStats($household);

            return $household;
        }


        // STEP 3: no match
        return null;
    }

    /**
     * Recalculate household summary statistics from members.
     */
    private function recalculateHouseholdStats(Household $household): void
    {
        $total = $household->members()->count();
        $adults = $minors = $seniors = $pwd = 0;

        foreach ($household->members as $member) {
            if ($member->birth_date) {
                $age = Carbon::parse($member->birth_date)->age;
                if ($age >= 65) {
                    $seniors++;
                } elseif ($age >= 18) {
                    $adults++;
                } else {
                    $minors++;
                }
            } else {
                $adults++;
            }
            if ($member->is_pwd) {
                $pwd++;
            }
        }

        $household->update(compact('total', 'adults', 'minors', 'seniors', 'pwd'));
    }

    /* -------------------------------------------------------------------------
     *  Main registration endpoint
     * -------------------------------------------------------------------------
     */
    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'verification_token' => ['required', 'string', 'min:10'],
                'password' => ['required', 'string', 'min:8'],
                'password_confirmation' => ['required', 'same:password'],

                // Resident fields
                'first_name' => ['required', 'string', 'max:100'],
                'middle_name' => ['nullable', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                // suffix column may not exist in DB yet (UI submits N/A)
                // 'suffix' => ['nullable', 'string', 'max:20'],
                'birth_date' => ['required', 'date'],
                'sex' => ['required', 'in:male,female'],
                'civil_status' => ['nullable', 'string', 'max:50'],

                'address_line' => ['required', 'string', 'max:255'],
                'phase' => ['nullable', 'string', 'max:50'],
                'contact_no' => ['nullable', 'string', 'max:30'],

                // OTP/email
                'email' => ['nullable', 'email', 'max:255'],

                // Socioeconomic
                'occupation' => ['nullable', 'string', 'max:80'],
                'employment_status' => ['nullable', 'string', 'max:50'],
                // Use UI options as “ranges” for analytics (stored as string keys like below_10k, 10k_20k, etc.)
                'monthly_income' => ['nullable', 'string', 'max:50'],
                'educational_attainment' => ['nullable', 'string', 'max:50'],
                'solo_parent' => ['nullable', 'boolean'],
                'pwd' => ['nullable', 'boolean'],
                'indigent' => ['nullable', 'boolean'],
'four_ps_beneficiary' => ['nullable', 'boolean'],

                // Verification inputs (optional in validation; controller logic enforces what it needs)
                'verification_type' => ['nullable', 'string', 'max:50'],
                'verification_id' => ['nullable', 'string', 'max:50'],

                // Guardian fields
                'guardian_full_name' => ['nullable', 'string', 'max:150'],
                'guardian_contact_no' => ['nullable', 'string', 'max:30'],
                'guardian_relationship' => ['nullable', 'string', 'max:50'],

                // Files (NOT making id_image_path and selfie_image_path required here)
                // We'll enforce required/not-required based on adult/minor logic below.
                'photo_path' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],

                // Government ID OR Proof of billing
                'id_image_path' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
                'proof_of_billing_path' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],

                // Selfie holding ID: optional (your request)
                'selfie_image_path' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],

                'child_doc' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            ]);

            // Compute age
            $age = Carbon::parse($data['birth_date'])->age;
            $isMinor = $age < 18;

            // Treat ID as “valid” if the form selected a type OR if an ID file / id field is actually present.
            // This prevents incorrect fallback to the proof-of-billing requirement.
            $hasValidIdType = ! empty($data['verification_type'])
                || $request->hasFile('id_image_path')
                || ! empty($data['verification_id']);


            if (! $isMinor) {
                // Adults: email required
                if (empty($data['email'])) {
                    return response()->json(['message' => 'Email required for 18 and above.'], 422);
                }

                if ($hasValidIdType) {
                    if (! $request->hasFile('id_image_path')) {
                        return response()->json(['message' => 'Please upload your Government ID when a valid ID type is selected.'], 422);
                    }
                    if (! $request->hasFile('selfie_image_path')) {
                        return response()->json(['message' => 'Please upload your selfie holding your ID.'], 422);
                    }
                } else {
                    if (! $request->hasFile('proof_of_billing_path')) {
                        return response()->json(['message' => 'Please upload proof of billing when no valid ID type is selected.'], 422);
                    }
                }

                // Adults: other requirements are handled by request fields.
            } else {
                // Minors: guardian required
                if (empty($data['guardian_full_name'])) {
                    return response()->json(['message' => 'Guardian full name required for minors.'], 422);
                }

                if ($hasValidIdType) {
                    if (! $request->hasFile('id_image_path')) {
                        return response()->json(['message' => 'Please upload your Government ID when a valid ID type is selected.'], 422);
                    }
                    if (! $request->hasFile('selfie_image_path')) {
                        return response()->json(['message' => 'Please upload your selfie holding your ID.'], 422);
                    }
                } else {
                    if (! $request->hasFile('proof_of_billing_path')) {
                        return response()->json(['message' => 'Please upload proof of billing when no valid ID type is selected.'], 422);
                    }
                }

                // Minors: child_doc is optional based on current UI logic.
            }

            // OTP verification
            $otp = OtpVerification::where('verification_token', $data['verification_token'])
                ->where('verified_at', '!=', null)
                ->first();
            if (! $otp || ($otp->purpose ?? '') !== 'resident_registration') {
                return response()->json(['message' => 'Invalid/expired verification token.'], 422);
            }

            $otpEmail   = mb_strtolower(trim($otp->email ?? ''));
            $givenEmail = mb_strtolower(trim($data['email'] ?? ''));
            if (! $otpEmail || ! $givenEmail || $otpEmail !== $givenEmail) {
                return response()->json([
                    'message' => 'OTP email mismatch.',
                    'debug' => [
                        'otp_email' => $otpEmail,
                        'given_email' => $givenEmail,
                    ],
                ], 422);
            }


            // Duplicate resident check
            $fn = $this->normalize($data['first_name']);
            $ln = $this->normalize($data['last_name']);
            $bd = $data['birth_date'];

            $dup = Resident::whereRaw('LOWER(TRIM(first_name)) = ?', [$fn])
                ->whereRaw('LOWER(TRIM(last_name)) = ?', [$ln])
                ->where('birth_date', $bd)
                ->first();

            if ($dup) {
                return response()->json([
                    'message'               => 'Resident name and birth date already registered.',
                    'existing_resident_id'  => $dup->id,
                ], 422);
            }

            // File handling
            $profilePath = $request->hasFile('photo_path')
                ? $request->file('photo_path')->store('resident_photos', 'public')
                : null;

            $idImagePath = null;
            if ($request->hasFile('id_image_path')) {
                $idImagePath = $request->file('id_image_path')->store('resident_ids', 'public');
            }

            $selfiePath = null;
            if (! $isMinor && $request->hasFile('selfie_image_path')) {
                $selfiePath = $request->file('selfie_image_path')->store('resident_selfies', 'public');
            }

            $childDocPath = null;
            if ($isMinor && $request->hasFile('child_doc')) {
                $childDocPath = $request->file('child_doc')->store('child_docs', 'public');
            }

            $proofOfBillingPath = null;
            if ($request->hasFile('proof_of_billing_path')) {
                $proofOfBillingPath = $request->file('proof_of_billing_path')->store('proof_of_billing', 'public');
            }

            // --- HOUSEHOLD LOGIC ---
            $addressLine = $data['address_line'];
            $phase       = $data['phase'] ?? null;

            $assignedHousehold = null; // Will hold the assigned household if any
            $isNewHousehold = false;

             // Create resident + optional user in transaction
             $resident = DB::transaction(function () use ($data, $profilePath, $idImagePath, $selfiePath, $childDocPath, $proofOfBillingPath, $isMinor, $addressLine, $phase, &$assignedHousehold, &$isNewHousehold) {
                $userId = null;
                if (! $isMinor && ! empty($data['email'])) {
                    if (User::where('email', $data['email'])->exists()) {
                        throw new \Exception('Email already registered.');
                    }
                    $user = User::create([
                        'email'    => $data['email'],
                        'password' => bcrypt($data['password']),
                        'role'     => 'resident',
                        'status'   => 'active',
                    ]);
                    $userId = $user->id;
                }

                  $resident = Resident::create([
                      'first_name'           => $data['first_name'],
                      'middle_name'          => $data['middle_name'] ?? null,
                      'last_name'            => $data['last_name'],
// suffix column may not exist in DB yet

                      'birth_date'           => $data['birth_date'],
                      'sex'                  => $data['sex'],
                      'civil_status'         => $data['civil_status'] ?? null,
                      'address_line'         => $data['address_line'],
                      'street'               => null,
                      'phase'                => $data['phase'] ?? null,
                      'contact_no'           => $data['contact_no'] ?? null,
                      'account_no'           => Resident::generateAccountNo(),
                      'otp_email'            => $isMinor ? null : ($data['email'] ?? null),
                      'occupation'           => $data['occupation'] ?? null,
                      'employment_status'    => $data['employment_status'] ?? null,
'monthly_income'       => ($data['monthly_income'] ?? null) !== null ? (string) $this->normalizeMonthlyIncome($data['monthly_income'] ?? null) : null,
                      'educational_attainment' => $data['educational_attainment'] ?? null,
                      'solo_parent'          => $data['solo_parent'] ?? false,
                      'pwd'                  => $data['pwd'] ?? false,
                      'indigent'             => $data['indigent'] ?? false,
                       'four_ps_beneficiary'  => $data['four_ps_beneficiary'] ?? false,
                      'verification_type'    => $data['verification_type'] ?? null,
                      'verification_status'  => 'verified',
                      'verified_at'          => now(),
                      'verification_id'      => $data['verification_id'] ?? null,
                      'photo_path'           => $profilePath,
                      'id_image_path'        => $idImagePath,
                      'selfie_image_path'    => $selfiePath,
                      'proof_of_billing_path'=> $proofOfBillingPath,
                      'household_id'         => null, // Will be updated by assignResidentToAppropriateHousehold
                      'user_id'              => $userId,
                      'registered_via'       => 'public_form',
                      'guardian_full_name'   => $isMinor ? ($data['guardian_full_name'] ?? null) : null,
                      'guardian_email'       => $isMinor ? ($data['email'] ?? null) : null,
                      'guardian_contact_no'  => $isMinor ? ($data['guardian_contact_no'] ?? null) : null,
                      'guardian_relationship' => $isMinor ? ($data['guardian_relationship'] ?? null) : null,
                      'child_doc_path'       => $childDocPath,
                  ]);
                
                // Call the helper to assign to household
                $assignedHousehold = $this->assignResidentToAppropriateHousehold($resident, $addressLine, $phase);

                if ($assignedHousehold) {
                    $isNewHousehold = $assignedHousehold->wasRecentlyCreated;
                }

                return $resident;
            });

            // Clear OTP
            $otp?->update(['verification_token' => null]);

            // Welcome email
            $fullName = trim($data['first_name'].' '.($data['middle_name'] ?? '').' '.$data['last_name']);
            try {
                Mail::to($data['email'])->send(new WelcomeEmail($fullName, $data['email']));
            } catch (\Exception $e) {
                Log::error('Welcome email failed: '.$e->getMessage());
            }

            return response()->json([
                'message'          => 'Registration successful!',
                'resident_id'      => $resident->id,
                'account_no'       => $resident->account_no,
                'household_code'   => $assignedHousehold ? $assignedHousehold->household_code : null,
                'household_id'     => $assignedHousehold ? $assignedHousehold->id : null,
                'is_new_household' => $isNewHousehold,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $class = get_class($e);

            Log::error('Registration failed: '.$msg.' | Class: '.$class, [
                'trace' => $e->getTraceAsString(),
            ]);

            // Return useful debug info to frontend so you can see the exact failure.
            // Remove these details once fixed.
            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'debug' => [
                    'error' => $msg,
                    'class' => $class,
                ],
            ], 500);
        }
    }

    /* -------------------------------------------------------------------------
     *  Helpers (keep existing ones)
     * -------------------------------------------------------------------------
     */
    private function norm(string $s): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
    }

    private function normalizeMonthlyIncome(?string $value): ?string
    {
        // monthly_income column likely numeric/short type in DB.
        // UI sends keys like: below_10k, 10k_20k, etc.
        // If DB can't store that key, map to a safe numeric-ish representation.
        if ($value === null) {
            return null;
        }

        $v = trim((string) $value);

        $map = [
            'below_10k' => '5000',
            '10k_20k' => '15000',
            '20k_30k' => '25000',
            '30k_50k' => '40000',
            '50k_above' => '60000',
        ];

        return $map[$v] ?? $v;
    }
}
