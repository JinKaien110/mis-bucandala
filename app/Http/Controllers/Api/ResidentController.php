<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResidentController extends Controller
{
    /* ===============================
        LIST (API)
    =============================== */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $residents = Resident::query()
            ->when($q, function ($query) use ($q) {
                $query->where('first_name', 'like', "%$q%")
                      ->orWhere('last_name', 'like', "%$q%")
                      ->orWhere('address_line', 'like', "%$q%")
                      ->orWhere('contact_no', 'like', "%$q%");
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'residents' => $residents
        ]);
    }

    /* ===============================
        STORE (Blade form)
    =============================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'middle_name'=> 'nullable|string|max:100',

            'sex'         => 'nullable|in:male,female',
            'birth_date'  => 'nullable|date',

            'address_line'=> 'required|string|max:255',
            'contact_no'  => 'nullable|string|max:30',
            'email'       => 'nullable|email',

            'civil_status'=> 'nullable|in:single,married,widowed,separated,divorced',
            'occupation'  => 'nullable|string|max:100',

            'verification_status' => 'required|in:unverified,pending,verified,rejected',
            'verification_id'     => 'nullable|string|max:100',
            'verification_type'   => 'nullable|string|max:50',

            'photo' => 'nullable|image|max:2048',

            'status' => 'nullable|in:active,inactive'
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('residents', 'public');
        }

        $data['status'] = $data['status'] ?? 'active';

        Resident::create($data);

        return redirect()
            ->route('admin.residents')
            ->with('success', 'Resident successfully added.');
    }

    /* ===============================
        SHOW (API)
    =============================== */
    public function show(Resident $resident)
    {
        return response()->json([
            'resident' => $resident
        ]);
    }

    /* ===============================
        UPDATE (Blade)
    =============================== */
    public function update(Request $request, Resident $resident)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'middle_name'=> 'nullable|string|max:100',

            'sex'        => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',

            'address_line'=> 'required|string|max:255',
            'contact_no' => 'nullable|string|max:30',
            'email'      => 'nullable|email',

            'civil_status'=> 'nullable|in:single,married,widowed,separated,divorced',
            'occupation' => 'nullable|string|max:100',

            'verification_status' => 'required|in:unverified,pending,verified,rejected',
            'verification_id'     => 'nullable|string|max:100',
            'verification_type'   => 'nullable|string|max:50',

            'photo' => 'nullable|image|max:2048',

            'status' => 'required|in:active,inactive'
        ]);

        if ($request->hasFile('photo')) {

            if ($resident->photo_path) {
                Storage::disk('public')->delete($resident->photo_path);
            }

            $data['photo_path'] =
                $request->file('photo')->store('residents', 'public');
        }

        $resident->update($data);

        return redirect()
            ->route('admin.residents')
            ->with('success', 'Resident updated successfully.');
    }

    /* ===============================
        TOGGLE STATUS
    =============================== */
    public function toggleStatus(Resident $resident)
    {
        $resident->status =
            $resident->status === 'active' ? 'inactive' : 'active';

        $resident->save();

        return response()->json([
            'message' => 'Status updated',
            'status'  => $resident->status
        ]);
    }
}
