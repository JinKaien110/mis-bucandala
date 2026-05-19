<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blotter;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CaseFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlotterController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date'],
            'search'    => ['nullable', 'string', 'max:255'],
            'page'      => ['nullable', 'integer', 'min:1'],
            'limit'     => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int)($validated['limit'] ?? 10);

        $q = Blotter::query()
            ->notArchived()
            ->with([
                'complainantResident:id,first_name,last_name,middle_name',
                'respondentResident:id,first_name,last_name,middle_name',
            ])
            ->orderByDesc('created_at');

        if (!empty($validated['date_from'])) {
            $q->whereDate('incident_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $q->whereDate('incident_date', '<=', $validated['date_to']);
        }

        if (!empty($validated['search'])) {
            $s = trim($validated['search']);
            $q->where(function ($w) use ($s) {
                $w->where('blotter_no', 'like', "%{$s}%")
                  ->orWhere('incident_type', 'like', "%{$s}%")
                  ->orWhere('incident_location', 'like', "%{$s}%")
                  ->orWhere('complainant_name', 'like', "%{$s}%")
                  ->orWhere('respondent_name', 'like', "%{$s}%");
            });
        }

        $blotters = $q->paginate($limit)->withQueryString();

        // Get residents for the modal
        $residents = Resident::query()
            ->with('user:id,email')
            ->select('id', 'user_id', 'first_name', 'middle_name', 'last_name', 'contact_no', 'email')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.blotters.index', [
            'blotters' => $blotters,
            'limit'    => $limit,
            'residents' => $residents,
            'filters'  => [
                'date_from' => $validated['date_from'] ?? null,
                'date_to'   => $validated['date_to'] ?? null,
                'search'    => $validated['search'] ?? null,
            ],
        ]);
    }

    // app/Http/Controllers/Admin/BlotterController.php
public function create()
{
    $residents = \App\Models\Resident::query()
        ->with('user:id,email')
        ->select('id', 'user_id', 'first_name', 'middle_name', 'last_name', 'contact_no', 'email')
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->get();

    return view('admin.blotters.create', compact('residents'));
}




    public function show(Blotter $blotter)
{
    $blotter->load(['case', 'complainantResident', 'respondentResident']);

    return view('admin.blotters.show', compact('blotter'));
}


    public function store(Request $request)
{
    // 1. Validate basic incident info + the hidden mode fields
    $validated = $request->validate([
        'incident_date'           => ['required', 'date'],
        'incident_type'           => ['required', 'string', 'max:255'],
        'incident_location'       => ['required', 'string', 'max:255'],
        'narrative'               => ['required', 'string'],
        'remarks'                 => ['nullable', 'string'],
        'complainant_mode'        => ['required', 'in:resident,manual'],
        'respondent_mode'         => ['required', 'in:resident,manual'],
        
        // IDs for resident mode
        'complainant_resident_id' => ['nullable', 'required_if:complainant_mode,resident', 'exists:residents,id'],
        'respondent_resident_id'  => ['nullable', 'required_if:respondent_mode,resident', 'exists:residents,id'],

        // Names for manual mode
        'complainant_name'        => ['nullable', 'required_if:complainant_mode,manual', 'string', 'max:255'],
        'respondent_name'         => ['nullable', 'string', 'max:255'],
    ]);

    // 2. Process Complainant (Combine Resident/Manual data into the final keys)
    if ($request->complainant_mode === 'resident') {
        $res = \App\Models\Resident::with('user')->findOrFail($request->complainant_resident_id);
        $validated['complainant_name']    = $res->last_name . ', ' . $res->first_name;
        // Use the input from the "_res" box if it's filled, otherwise use DB
        $validated['complainant_contact'] = $request->filled('complainant_contact_res') ? $request->complainant_contact_res : $res->contact_no;
        $validated['complainant_email']   = $request->filled('complainant_email_res') ? $request->complainant_email_res : $res->email;
    } else {
        // Manual mode uses the direct names from validation
        $validated['complainant_contact'] = $request->complainant_contact;
        $validated['complainant_email']   = $request->complainant_email;
    }

    // 3. Process Respondent
    if ($request->respondent_mode === 'resident') {
        $res = \App\Models\Resident::with('user')->findOrFail($request->respondent_resident_id);
        $validated['respondent_name']    = $res->last_name . ', ' . $res->first_name;
        $validated['respondent_contact'] = $request->filled('respondent_contact_res') ? $request->respondent_contact_res : $res->contact_no;
        $validated['respondent_email']   = $request->filled('respondent_email_res') ? $request->respondent_email_res : $res->email;
    } else {
        $validated['respondent_contact'] = $request->respondent_contact;
        $validated['respondent_email']   = $request->respondent_email;
    }

    $userId = auth()->id();

    try {
        $blotter = DB::transaction(function () use ($validated, $userId) {
            $year = now()->year;

            $last = Blotter::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $next = 1;
            if ($last && preg_match('/^BLT\-' . $year . '\-(\d{6})$/', $last->blotter_no, $m)) {
                $next = ((int)$m[1]) + 1;
            }

            // Merge final data
            $finalData = array_merge($validated, [
                'blotter_no'  => 'BLT-' . $year . '-' . str_pad((string)$next, 6, '0', STR_PAD_LEFT),
                'recorded_by' => $userId,
                'status'      => 'filed'
            ]);

            return Blotter::create($finalData);
        });

        return redirect()->route('admin.blotters.index')
            ->with('success', "Blotter record generated: {$blotter->blotter_no}");

    } catch (\Exception $e) {
        Log::error("Blotter Store Error: " . $e->getMessage());
        return back()->withErrors(['error' => 'Saving failed: ' . $e->getMessage()])->withInput();
    }
}

    private function nextCaseNo(): string
{
    $year = now()->year;

    $last = CaseFile::whereYear('created_at', $year)
        ->lockForUpdate()
        ->orderByDesc('id')
        ->first();

    $next = 1;

    if ($last && preg_match('/^CASE\-' . $year . '\-(\d{6})$/', $last->case_no, $m)) {
        $next = ((int) $m[1]) + 1;
    }

    return 'CASE-' . $year . '-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
}

        public function openCase(Blotter $blotter, Request $request)
    {
        if ($blotter->case) {
            return back()->with('error', 'This blotter already has an ongoing case.');
        }

        $case = DB::transaction(function () use ($blotter, $request) {
            return CaseFile::create([
                'case_no' => $this->nextCaseNo(),
                'blotter_id' => $blotter->id,
                'status' => 'ongoing',
                'opened_at' => now(),
                'handled_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('admin.cases.show', $case)->with('success', 'Case opened.');
    }

    public function update(Request $request, Blotter $blotter)
    {
        $validated = $request->validate([
            'incident_date'     => ['required', 'date'],
            'incident_type'     => ['required', 'string', 'max:255'],
            'incident_location' => ['required', 'string', 'max:255'],
            'narrative'         => ['required', 'string'],
            'remarks'           => ['nullable', 'string'],

            'complainant_resident_id' => ['nullable', 'integer', 'exists:residents,id'],
            'respondent_resident_id'  => ['nullable', 'integer', 'exists:residents,id'],

            'complainant_name'    => ['nullable', 'string', 'max:255'],
            'respondent_name'     => ['nullable', 'string', 'max:255'],
            'complainant_contact' => ['nullable', 'string', 'max:50'],
            'respondent_contact'  => ['nullable', 'string', 'max:50'],
            'complainant_email' => ['nullable', 'email', 'max:255'],
            'respondent_email'  => ['nullable', 'email', 'max:255'],

        ]);

        if (empty($validated['complainant_resident_id']) && empty($validated['complainant_name'])) {
            return back()
    ->withErrors(['complainant' => 'Provide complainant resident or name.'])
    ->withInput();

        }

        if (empty($validated['respondent_resident_id']) && empty($validated['respondent_name'])) {
            return back()
    ->withErrors(['respondent' => 'Provide respondent resident or name.'])
    ->withInput();

        }

        $blotter->update($validated);

        return response()->json($blotter);
    }
}
