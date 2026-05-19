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
     * Attach resident to household. Sets relationship: Head if first member, else Member.
     */
    private function attachResidentToHousehold(Household $household, Resident $resident): void
    {
        $count = $household->members()->count();
        $relationship = ($count === 0) ? 'Head' : 'Member';

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

            $household = $this->createHousehold($addressLine, $phase);

            // include current resident
            $allResidents = $matchingResidents->values()->push($resident);

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
                'verification_token'      => ['required', 'string', 'min:10'],
                'password'                => ['required', 'string', 'min:8'],
                'password_confirmation'   => ['required', 'same:password'],

                // Resident fields
                'first_name'              => ['required', 'string', 'max:100'],
                'middle_name'             => ['nullable', 'string', 'max:100'],
                'last_name'               => ['required', 'string', 'max:100'],
                'birth_date'              => ['required', 'date'],
                'sex'                     => ['required', 'in:male,female'],
                'civil_status'            => ['nullable', 'string', 'max:50'],

                'address_line'            => ['required', 'string', 'max:255'],
                // 'street' removed
                'phase'                   => ['nullable', 'string', 'max:50'],
                'contact_no'              => ['nullable', 'string', 'max:30'],

                'email'                   => ['nullable', 'email', 'max:255'],
                'occupation'              => ['nullable', 'string', 'max:80'],
                'verification_type'       => ['required', 'string', 'max:50'],
                'verification_id'         => ['nullable', 'string', 'max:50'],

                // Guardian fields
                'guardian_full_name'      => ['nullable', 'string', 'max:150'],
                'guardian_contact_no'     => ['nullable', 'string', 'max:30'],
                'guardian_relationship'   => ['nullable', 'string', 'max:50'],

                // Files
                'photo_path'              => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
                'id_image_path'           => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
                'selfie_image_path'       => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
                'child_doc'               => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            ]);

            // Compute age
            $age = Carbon::parse($data['birth_date'])->age;
            $isMinor = $age < 18;

            // Age-based requirements
            if (! $isMinor) {
                if (empty($data['email'])) {
                    return response()->json(['message' => 'Email required for 18 and above.'], 422);
                }
                if (! $request->hasFile('photo_path') || ! $request->hasFile('selfie_image_path')) {
                    return response()->json(['message' => 'ID photo and selfie required for adults.'], 422);
                }
            } else {
                if (empty($data['guardian_full_name'])) {
                    return response()->json(['message' => 'Guardian full name required for minors.'], 422);
                }
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
                return response()->json(['message' => 'OTP email mismatch.'], 422);
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

            if (! $request->hasFile('id_image_path')) {
                throw new \Exception('Government ID is required.');
            }
            $idImagePath = $request->file('id_image_path')->store('resident_ids', 'public');

            $selfiePath = null;
            if (! $isMinor) {
                if (! $request->hasFile('selfie_image_path')) {
                    throw new \Exception('Selfie with ID required for adults.');
                }
                $selfiePath = $request->file('selfie_image_path')->store('resident_selfies', 'public');
            }

            $childDocPath = null;
            if ($isMinor && $request->hasFile('child_doc')) {
                $childDocPath = $request->file('child_doc')->store('child_docs', 'public');
            }

            // --- HOUSEHOLD LOGIC ---
            $addressLine = $data['address_line'];
            $phase       = $data['phase'] ?? null;

            $assignedHousehold = null; // Will hold the assigned household if any
            $isNewHousehold = false;

            // Create resident + optional user in transaction
            $resident = DB::transaction(function () use ($data, $profilePath, $idImagePath, $selfiePath, $childDocPath, $isMinor, $addressLine, $phase, &$assignedHousehold, &$isNewHousehold) {
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
                    'verification_type'    => $data['verification_type'],
                    'verification_status'  => 'verified',
                    'verified_at'          => now(),
                    'verification_id'      => $data['verification_id'] ?? null,
                    'photo_path'           => $profilePath,
                    'id_image_path'        => $idImagePath,
                    'selfie_image_path'    => $selfiePath,
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
            return response()->json(['message' => 'Please fill in all required fields.'], 422);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Log::error('Registration failed: '.$msg.' | Class: '.get_class($e));

            if (str_contains($msg, 'Duplicate entry') && str_contains($msg, 'email_unique')) {
                return response()->json(['message' => 'Email already registered.'], 422);
            }
            if (str_contains($msg, 'already registered')) {
                return response()->json(['message' => 'Resident already registered.'], 422);
            }
            if (str_contains($msg, 'required')) {
                return response()->json(['message' => $msg], 422);
            }
            if (str_contains($msg, 'Connection could not be established')) {
                return response()->json(['message' => "Please try again"], 422);
            }
            return response()->json(['message' => 'Registration failed. Please try again.'], 500);
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
}
