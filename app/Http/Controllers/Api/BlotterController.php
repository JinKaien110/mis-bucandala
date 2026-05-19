<?php
// app/Http/Controllers/Admin/BlotterController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blotter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BlotterController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int)($validated['limit'] ?? 10);

        $q = Blotter::query()
            ->with([
                'recordedBy:id,email',
                'complainantResident:id,first_name,last_name,middle_name',
                'respondentResident:id,first_name,last_name,middle_name',
            ])
            ->orderByDesc('incident_date');

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

        return view('admin.blotters.index', compact('limit'));
    }

    public function show(Blotter $blotter)
    {
        $blotter->load([
            'recordedBy:id,email',
            'complainantResident:id,first_name,last_name,middle_name',
            'respondentResident:id,first_name,last_name,middle_name',
        ]);

        return response()->json($blotter);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_date' => ['required', 'date'],
            'incident_type' => ['required', 'string', 'max:255'],
            'incident_location' => ['required', 'string', 'max:255'],
            'narrative' => ['required', 'string'],

            'remarks' => ['nullable', 'string'],

            'complainant_resident_id' => ['nullable', 'integer', 'exists:residents,id'],
            'respondent_resident_id' => ['nullable', 'integer', 'exists:residents,id'],

            'complainant_name' => ['nullable', 'string', 'max:255'],
            'respondent_name' => ['nullable', 'string', 'max:255'],
            'complainant_contact' => ['nullable', 'string', 'max:50'],
            'respondent_contact' => ['nullable', 'string', 'max:50'],
        ]);

        // require complainant identity (either resident_id or name)
        if (empty($validated['complainant_resident_id']) && empty($validated['complainant_name'])) {
            return response()->json([
                'message' => 'Complainant is required (resident or name).',
                'errors' => ['complainant' => ['Provide complainant_resident_id or complainant_name.']]
            ], 422);
        }

        // require respondent identity (either resident_id or name)
        if (empty($validated['respondent_resident_id']) && empty($validated['respondent_name'])) {
            return response()->json([
                'message' => 'Respondent is required (resident or name).',
                'errors' => ['respondent' => ['Provide respondent_resident_id or respondent_name.']]
            ], 422);
        }

        $userId = auth()->id();

        $blotter = DB::transaction(function () use ($validated, $userId) {
            $year = now()->year;

            // lock last row for this year to avoid duplicate series
            $last = Blotter::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $next = 1;
            if ($last && preg_match('/^BLT\-' . $year . '\-(\d{6})$/', $last->blotter_no, $m)) {
                $next = ((int)$m[1]) + 1;
            }

            $validated['blotter_no'] = 'BLT-' . $year . '-' . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
            $validated['recorded_by'] = $userId;

            return Blotter::create($validated);
        });

        return response()->json($blotter, 201);
    }

    public function update(Request $request, Blotter $blotter)
    {
        $validated = $request->validate([
            'incident_date' => ['required', 'date'],
            'incident_type' => ['required', 'string', 'max:255'],
            'incident_location' => ['required', 'string', 'max:255'],
            'narrative' => ['required', 'string'],

            'remarks' => ['nullable', 'string'],

            'complainant_resident_id' => ['nullable', 'integer', 'exists:residents,id'],
            'respondent_resident_id' => ['nullable', 'integer', 'exists:residents,id'],

            'complainant_name' => ['nullable', 'string', 'max:255'],
            'respondent_name' => ['nullable', 'string', 'max:255'],
            'complainant_contact' => ['nullable', 'string', 'max:50'],
            'respondent_contact' => ['nullable', 'string', 'max:50'],
        ]);

        if (empty($validated['complainant_resident_id']) && empty($validated['complainant_name'])) {
            return response()->json([
                'message' => 'Complainant is required (resident or name).',
                'errors' => ['complainant' => ['Provide complainant_resident_id or complainant_name.']]
            ], 422);
        }

        if (empty($validated['respondent_resident_id']) && empty($validated['respondent_name'])) {
            return response()->json([
                'message' => 'Respondent is required (resident or name).',
                'errors' => ['respondent' => ['Provide respondent_resident_id or respondent_name.']]
            ], 422);
        }

        $blotter->update($validated);

        return response()->json($blotter);
    }


}
