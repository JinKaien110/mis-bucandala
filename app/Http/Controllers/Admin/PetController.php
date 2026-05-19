<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\Resident;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q', '');
        $species = $request->query('species', '');

        $pets = Pet::with('resident')
            ->notArchived()
            ->when($search, fn($q) => $q->where('nickname', 'like', "%{$search}%")
                ->orWhereHas('resident', fn($r) => $r->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")))
            ->when($species, fn($q) => $q->where('species', $species))
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.pets.index', [
            'pets' => $pets,
            'search' => $search,
            'species' => $species,
        ]);
    }

    public function create()
    {
        $residents = Resident::where('status', 'active')
            ->whereHas('user')
            ->with('user:id,email')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name']);
        return view('admin.pets.create', compact('residents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'resident_id' => ['required', 'exists:residents,id'],
            'nickname' => ['required', 'string', 'max:100'],
            'species' => ['required', 'string', 'max:50'],
            'breed' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'sex' => ['required', 'in:male,female'],
            'color' => ['nullable', 'string', 'max:100'],
            'vaccination_status' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        // Verify resident has email
        $resident = Resident::with('user')->findOrFail($data['resident_id']);
        if (!$resident->email) {
            return back()->with('error', 'Resident must have an email address to register a pet.');
        }

        Pet::create($data);

        return redirect()->route('admin.pets.index')
            ->with('success', 'Pet registered successfully.');
    }

    public function show(Pet $pet)
    {
        $pet->load('resident');
        return view('admin.pets.show', compact('pet'));
    }

    public function edit(Pet $pet)
    {
        $residents = Resident::where('status', 'active')
            ->whereHas('user')
            ->with('user:id,email')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name']);
        return view('admin.pets.edit', compact('pet', 'residents'));
    }

    public function update(Request $request, Pet $pet)
    {
        $data = $request->validate([
            'resident_id' => ['required', 'exists:residents,id'],
            'nickname' => ['required', 'string', 'max:100'],
            'species' => ['required', 'string', 'max:50'],
            'breed' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'sex' => ['required', 'in:male,female'],
            'color' => ['nullable', 'string', 'max:100'],
            'vaccination_status' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $pet->update($data);

        return redirect()->route('admin.pets.show', $pet)
            ->with('success', 'Pet updated successfully.');
    }

    public function destroy(Pet $pet)
    {
        $pet->archive();
        return redirect()->route('admin.pets.index')
            ->with('success', 'Pet archived successfully.');
    }
}
