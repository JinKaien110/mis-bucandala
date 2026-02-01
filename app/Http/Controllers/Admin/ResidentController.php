<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $residents = Resident::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('first_name', 'like', "%{$q}%")
                       ->orWhere('middle_name', 'like', "%{$q}%")
                       ->orWhere('last_name', 'like', "%{$q}%")
                       ->orWhere('address_line', 'like', "%{$q}%")
                       ->orWhere('contact_no', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('id', 'desc')
            ->get(['id','first_name','middle_name','last_name','sex','birth_date','address_line','contact_no','email','photo_path','verification_status','status','created_at']);


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
            'email' => ['nullable','email','max:255'],
            'civil_status' => ['nullable', Rule::in(['single','married','widowed','separated','divorced'])],
            'photo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],

            'occupation' => ['nullable','string','max:255'],

            // verification fields optional
            'verification_id' => ['nullable','string','max:255'],
            'verification_type' => ['nullable', Rule::in(['barangay_id','national_id','passport','drivers_license'])],
            'verification_status' => ['nullable', Rule::in(['unverified','pending','verified','rejected'])],

            'status' => ['nullable', Rule::in(['active','inactive'])],
        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('residents', 'public');
        }


        $resident = Resident::create([
            ...$data,
            'photo_path' => $photoPath,
            'barangay' => $data['barangay'] ?? 'Bucandala 1',
            'city' => $data['city'] ?? 'Imus',
            'province' => $data['province'] ?? 'Cavite',
            'verification_status' => $data['verification_status'] ?? 'unverified',
            'status' => $data['status'] ?? 'active',
        ]);

        // Blade submit → back with flash
        if (!$request->expectsJson()) {
            return back()->with('success', 'Resident created successfully.');
        }

        // fetch/ajax → json
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
            'email' => ['nullable','email','max:255'],
            'civil_status' => ['nullable', Rule::in(['single','married','widowed','separated','divorced'])],
            'photo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],

            'occupation' => ['nullable','string','max:255'],

            'verification_id' => ['nullable','string','max:255'],
            'verification_type' => ['nullable', Rule::in(['barangay_id','national_id','passport','drivers_license'])],
            'verification_status' => ['required', Rule::in(['unverified','pending','verified','rejected'])],

            'status' => ['required', Rule::in(['active','inactive'])],
        ]);

        if ($request->hasFile('photo')) {
    $photoPath = $request->file('photo')->store('residents', 'public');
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
}
