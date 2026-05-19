<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $sex = $request->query('sex');
        $civilStatus = $request->query('civil_status');
        $occupation = $request->query('occupation');
        $status = $request->query('status');
        $minAge = $request->query('min_age');
        $maxAge = $request->query('max_age');

        $residents = Resident::query()
            ->notArchived()
            // Search query
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('first_name', 'like', "%{$q}%")
                       ->orWhere('middle_name', 'like', "%{$q}%")
                       ->orWhere('last_name', 'like', "%{$q}%")
                       ->orWhere('address_line', 'like', "%{$q}%")
                       ->orWhere('contact_no', 'like', "%{$q}%")
                       ->orWhere('occupation', 'like', "%{$q}%")
                       ->orWhereHas('user', function ($userQuery) use ($q) {
                           $userQuery->where('email', 'like', "%{$q}%");
                       });
                });
            })
            // Filter by sex
            ->when($sex, function ($query) use ($sex) {
                $query->where('sex', $sex);
            })
            // Filter by civil status
            ->when($civilStatus, function ($query) use ($civilStatus) {
                $query->where('civil_status', $civilStatus);
            })
            // Filter by occupation
            ->when($occupation, function ($query) use ($occupation) {
                $query->where('occupation', 'like', "%{$occupation}%");
            })
            // Filter by status
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            // Filter by minimum age
            ->when($minAge, function ($query) use ($minAge) {
                $maxBirthDate = now()->subYears($minAge)->format('Y-m-d');
                $query->whereDate('birth_date', '<=', $maxBirthDate);
            })
            // Filter by maximum age
            ->when($maxAge, function ($query) use ($maxAge) {
                $minBirthDate = now()->subYears($maxAge + 1)->format('Y-m-d');
                $query->whereDate('birth_date', '>', $minBirthDate);
            })
            ->orderBy('id', 'desc')
            ->get(['id','first_name','middle_name','last_name','sex','birth_date','address_line','contact_no','photo_path', 'selfie_image_path', 'verification_status','status','archived_at','created_at']);


        return response()->json(['residents' => $residents]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required','string','max:255'],
            'middle_name' => ['nullable','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'sex' => ['nullable', Rule::in(['male','female'])],
            'birth_date' => ['nullable','date'],

            'address_line' => ['required','string','max:255'],
            'barangay' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:255'],
            'province' => ['nullable','string','max:255'],

            'contact_no' => ['nullable','string','max:50'],
            'email' => ['required','email','max:255','unique:users,email'],
            'civil_status' => ['nullable', Rule::in(['single','married','widowed','separated','divorced'])],
            'selfie_image_path' => ['required','image','mimes:jpg,jpeg,png,webp','max:2048'],

            'occupation' => ['nullable', Rule::in(['Technician','Teacher','Businessman','Nurse','Delivery Rider','Student','Retired','Cashier','Photographer','Office Clerk','Other'])],

            // verification fields optional
            'verification_id' => ['nullable','string','max:255'],
            'verification_type' => ['nullable', Rule::in(['philid','drivers_license','passport','postal','voters','umid','tin','pagibig','schoolid', 'barangay_id', 'national_id'])],

            'password' => ['required','string','min:8'],
        ]);

        $selfiePath = null;

        if ($request->hasFile('selfie_image_path')) {
            $selfiePath = $request->file('selfie_image_path')->store('resident_selfies', 'public');
        }

        $user = User::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'resident',
            'status' => 'active',
            'registered_via' => 'admin',
        ]);

         $resident = Resident::create([
             'first_name' => $data['first_name'],
             'middle_name' => $data['middle_name'] ?? null,
             'last_name' => $data['last_name'],
             'sex' => $data['sex'] ?? null,
             'birth_date' => $data['birth_date'] ?? null,
             'address_line' => $data['address_line'],
             'barangay' => $data['barangay'] ?? 'Bucandala 1',
             'city' => $data['city'] ?? 'Imus',
             'province' => $data['province'] ?? 'Cavite',
             'contact_no' => $data['contact_no'] ?? null,
             'account_no' => Resident::generateAccountNo(),
             'email' => $data['email'],
             'civil_status' => $data['civil_status'] ?? null,
             'occupation' => $data['occupation'] ?? null,
             'selfie_image_path' => $selfiePath,

             'verification_type' => $data['verification_type'] ?? null,
             'verification_id' => $data['verification_id'] ?? null,
             'verification_status' => 'verified',
             'verified_at' => now(),
             'verified_by' => auth()->id(),
             'status' => 'active',
             'user_id' => $user->id,
             'household_id' => null,
             'registered_via' => 'admin',
         ]);

        Mail::raw(
            "Hello {$resident->first_name},\n\n" .
            "Your resident account has been created. Use the temporary password below to sign in:\n\n" .
            "Email: {$resident->email}\n" .
            "Temporary Password: {$data['password']}\n\n" .
            "Please log in and update your password as soon as possible.\n\n" .
            "Thank you,\nBarangay MIS Team",
            function ($message) use ($resident) {
                $message->to($resident->email)
                    ->subject('Barangay MIS Temporary Password');
            }
        );

        if (!$request->expectsJson()) {
            return back()->with('success', 'Resident created successfully and password notification sent.');
        }

        return response()->json([
            'message' => 'Resident created',
            'resident' => $resident
        ], 201);
    }

    public function show(Resident $resident)
    {
        return response()->json(['resident' => $resident]);
    }

    public function update(Request $request, Resident $resident)
    {
        $data = $request->validate([
            'first_name' => ['required','string','max:255'],
            'middle_name' => ['nullable','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'sex' => ['nullable', Rule::in(['male','female'])],
            'birth_date' => ['nullable','date'],

            'address_line' => ['required','string','max:255'],
            'barangay' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:255'],
            'province' => ['nullable','string','max:255'],

            'contact_no' => ['nullable','string','max:50'],
            'civil_status' => ['nullable', Rule::in(['single','married','widowed','separated','divorced'])],
            'selfie_image_path' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],

            'occupation' => ['nullable','string','max:255'],

            'verification_id' => ['nullable','string','max:255'],
            'verification_type' => ['nullable', Rule::in(['barangay_id','national_id','passport','drivers_license', 'schoolid'])],
            'verification_status' => ['required', Rule::in(['unverified','pending','verified','rejected'])],

            'status' => ['required', Rule::in(['active','inactive'])],
        ]);

        if ($request->hasFile('selfie_image_path')) {
    $photoPath = $request->file('selfie_image_path')->store('resident_selfies', 'public');
    $resident->photo_path = $photoPath;
}

        $resident->update($data);
        $resident->save();


        if (!$request->expectsJson()) {
            return back()->with('success', 'Resident updated successfully.');
        }

        return response()->json(['message' => 'Resident updated', 'resident' => $resident]);
    }

    public function toggleStatus(Resident $resident)
    {
        $resident->status = $resident->status === 'active' ? 'inactive' : 'active';
        $resident->save();

        return response()->json(['message' => 'Status updated', 'status' => $resident->status]);
    }

    public function destroy(Resident $resident)
    {
        $resident->archive();
        return response()->json(['message' => 'Resident archived successfully']);
    }

    public function archive(Resident $resident)
    {
        $resident->archive();
        return response()->json(['message' => 'Resident archived successfully']);
    }

    public function restore(Resident $resident)
    {
        $resident->restore();
        return response()->json(['message' => 'Resident restored successfully']);
    }
}
