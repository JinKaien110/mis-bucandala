<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangayOfficial;
use App\Models\BarangayTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfficialController extends Controller
{
    public function index(Request $request)
    {
        $termId = $request->query('term');
        
        if ($termId) {
            $currentTerm = BarangayTerm::findOrFail($termId);
            $officials = BarangayOfficial::with('term')
                ->notArchived()
                ->where('barangay_term_id', $termId)
                ->orderBy('position')
                ->paginate(15);
        } else {
            $currentTerm = BarangayTerm::active()->first();
            $officials = BarangayOfficial::with('term')
                ->notArchived()
                ->whereHas('term', function($q) {
                    $q->where('is_active', true)->where('is_archived', false);
                })
                ->orderBy('position')
                ->paginate(15);
        }
        
        $terms = BarangayTerm::orderByDesc('term_start')->get();

        return view('admin.officials.index', [
            'officials' => $officials,
            'terms' => $terms,
            'currentTerm' => $currentTerm,
        ]);
    }

    public function termsIndex()
    {
        $view = request()->query('view', 'active');
        
        if ($view === 'archived') {
            $terms = BarangayTerm::withCount('officials')
                ->archived()
                ->orderByDesc('term_start')
                ->paginate(15);
        } else {
            $terms = BarangayTerm::withCount('officials')
                ->active()
                ->orderByDesc('term_start')
                ->paginate(15);
        }

        return view('admin.officials.terms', [
            'terms' => $terms,
            'view' => $view,
        ]);
    }

    public function storeTerm(Request $request)
    {
        $data = $request->validate([
            'term_start' => ['required', 'integer', 'min:2000', 'max:2100'],
            'term_end' => ['nullable', 'integer', 'min:2000', 'max:2100', 'gte:term_start'],
            'title' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        BarangayTerm::create($data);

        return redirect()->route('admin.officials.index')
            ->with('success', 'Term created successfully.');
    }

    public function updateTerm(Request $request, BarangayTerm $term)
    {
        $data = $request->validate([
            'term_start' => ['required', 'integer', 'min:2000', 'max:2100'],
            'term_end' => ['nullable', 'integer', 'min:2000', 'max:2100', 'gte:term_start'],
            'title' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $term->update($data);

        return redirect()->route('admin.officials.terms')
            ->with('success', 'Term updated successfully.');
    }

    public function archiveTerm(BarangayTerm $term)
    {
        $term->update([
            'is_archived' => true,
            'is_active' => false,
        ]);
        return back()->with('success', 'Term archived successfully.');
    }

    public function unarchiveTerm(BarangayTerm $term)
    {
        $term->update([
            'is_archived' => false,
            'is_active' => true,
        ]);
        return back()->with('success', 'Term restored successfully.');
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:100'],
            'committee' => ['nullable', 'string', 'max:100'],
            'contact_no' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'barangay_term_id' => ['required', 'exists:barangay_terms,id'],
            'notes' => ['nullable', 'string'],
            'photo_path' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo_path')) {
            $photo = $request->file('photo_path');
            $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('officials', $filename, 'public');
            $data['photo_path'] = 'officials/' . $filename;
        }

        BarangayOfficial::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Official added successfully.',
            'redirect' => route('admin.officials.index', ['term' => $data['barangay_term_id']])
        ]);
    }

    public function update(Request $request, BarangayOfficial $official)
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'position' => ['required', 'string', 'max:100'],
                'committee' => ['nullable', 'string', 'max:100'],
                'contact_no' => ['nullable', 'string', 'max:20'],
                'email' => ['nullable', 'email', 'max:255'],
                'barangay_term_id' => ['required', 'exists:barangay_terms,id'],
                'notes' => ['nullable', 'string'],
                'photo_path' => ['nullable', 'image', 'max:5048', 'mimes:jpeg,png,jpg,gif'],
            ]);

            // Handle photo upload
            if ($request->hasFile('photo_path')) {
                // Delete old photo if exists
                if ($official->photo_path) {
                    Storage::disk('public')->delete($official->photo_path);
                }
                
                $photo = $request->file('photo_path');
                $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('officials', $filename, 'public');
                $data['photo_path'] = 'officials/' . $filename;
            }

            $official->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Official updated successfully.',
                'redirect' => route('admin.officials.index', ['term' => $data['barangay_term_id']])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function destroy(BarangayOfficial $official)
    {
        $termId = $official->barangay_term_id;
        $official->archive();
        return redirect()->route('admin.officials.index', ['term' => $termId])
            ->with('success', 'Official archived successfully.');
    }

    // API methods for modals
    public function getCreateData()
    {
        $terms = BarangayTerm::active()->orderByDesc('term_start')->get();
        return response()->json([
            'terms' => $terms
        ]);
    }

    public function getEditData(BarangayOfficial $official)
    {
        $terms = BarangayTerm::orderByDesc('term_start')->get();
        return response()->json([
            'official' => $official,
            'terms' => $terms
        ]);
    }

    public function getShowData(BarangayOfficial $official)
    {
        return response()->json([
            'official' => [
                'id' => $official->id,
                'name' => $official->name,
                'position' => $official->position,
                'committee' => $official->committee,
                'contact_no' => $official->contact_no,
                'email' => $official->email,
                'notes' => $official->notes,
                'photo_path' => $official->photo_path,
                'barangay_term_id' => $official->barangay_term_id,
                'term' => $official->term ? [
                    'term_label' => $official->term->term_label
                ] : null
            ]
        ]);
    }

    public function getTermCreateData()
    {
        return response()->json([]);
    }

    public function getTermEditData(BarangayTerm $term)
    {
        return response()->json([
            'term' => $term
        ]);
    }
}
