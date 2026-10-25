<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blotter;
use App\Models\DocumentType;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CaseFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

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
            ->leftJoin('cases', 'cases.blotter_id', '=', 'blotters.id')
            ->select('blotters.*')
            ->whereNull('blotters.archived_at')
            ->with([
                'complainantResident:id,first_name,last_name,middle_name',
                'respondentResident:id,first_name,last_name,middle_name',
            ])
            ->orderByRaw("CASE
                WHEN cases.id IS NULL THEN 0
                WHEN LOWER(cases.status) IN ('ongoing', 'open', 'scheduled') THEN 1
                WHEN LOWER(cases.status) IN ('referred', 'settled', 'dismissed', 'archived') THEN 2
                ELSE 3
            END")
            ->orderByRaw("CASE
                WHEN cases.id IS NULL OR LOWER(cases.status) IN ('ongoing', 'open', 'scheduled') THEN blotters.created_at
            END ASC")
            ->orderByRaw("CASE
                WHEN LOWER(cases.status) NOT IN ('ongoing', 'open', 'scheduled') AND cases.id IS NOT NULL THEN blotters.created_at
            END DESC")
            ->orderByRaw("CASE
                WHEN cases.id IS NULL OR LOWER(cases.status) IN ('ongoing', 'open', 'scheduled') THEN blotters.id
            END ASC")
            ->orderByRaw("CASE
                WHEN LOWER(cases.status) NOT IN ('ongoing', 'open', 'scheduled') AND cases.id IS NOT NULL THEN blotters.id
            END DESC");

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

        $residents = Resident::query()
            ->with('user:id,email')
            ->select('id', 'user_id', 'first_name', 'middle_name', 'last_name', 'contact_no', 'address_line')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.blotters.show', compact('blotter', 'residents'));
    }

    public function updateRespondentAndMeeting(Request $request, Blotter $blotter)
    {
        $validated = $request->validate([
            'respondent_mode' => ['required', 'in:resident,manual'],
            'respondent_resident_id' => ['nullable', 'required_if:respondent_mode,resident', 'integer', 'exists:residents,id'],
            'respondent_name' => ['nullable', 'required_if:respondent_mode,manual', 'string', 'max:255'],
            'respondent_contact' => ['nullable', 'string', 'max:50'],
            'respondent_email' => ['nullable', 'email', 'max:255'],
            'respondent_address_line' => ['nullable', 'string', 'max:255'],
            'initial_meeting' => ['nullable', 'required_if:_action,download_patawag', 'date'],
            '_action' => ['required', 'in:save,download_patawag'],
        ]);

        if ($validated['respondent_mode'] === 'resident') {
            $resident = Resident::with('user')->findOrFail($validated['respondent_resident_id']);
            $blotter->respondent_resident_id = $resident->id;
            $blotter->respondent_name = trim($resident->last_name . ', ' . $resident->first_name);
            $blotter->respondent_contact = $resident->contact_no;
            $blotter->respondent_email = $resident->email;
            $blotter->respondent_address_line = $resident->address_line;
        } else {
            $blotter->respondent_resident_id = null;
            $blotter->respondent_name = $validated['respondent_name'];
            $blotter->respondent_contact = $validated['respondent_contact'] ?? null;
            $blotter->respondent_email = $validated['respondent_email'] ?? null;
            $blotter->respondent_address_line = $validated['respondent_address_line'] ?? null;
        }

        $blotter->initial_meeting = $validated['initial_meeting'] ?? null;
        $blotter->save();

        if ($validated['_action'] === 'download_patawag') {
            return $this->downloadPatawagComplainant($blotter);
        }

        return redirect()
            ->route('admin.blotters.show', $blotter)
            ->with('success', 'Respondent and initial meeting details updated.');
    }

    private function downloadPatawagComplainant(Blotter $blotter)
    {
        $documentType = DocumentType::query()
            ->notArchived()
            ->whereRaw('LOWER(TRIM(name)) = ?', ['patawag complainant'])
            ->first();

        if (!$documentType || !$documentType->template_path) {
            return redirect()
                ->route('admin.blotters.show', $blotter)
                ->with('error', 'The Patawag Complainant document type or its DOCX template is not available. The blotter details were saved.');
        }

        $templatePath = storage_path('app/public/' . $documentType->template_path);
        if (!is_file($templatePath)) {
            return redirect()
                ->route('admin.blotters.show', $blotter)
                ->with('error', 'The Patawag Complainant DOCX template file is missing. The blotter details were saved.');
        }

        $scheduledAt = $blotter->initial_meeting;
        if (!$scheduledAt) {
            return redirect()
                ->route('admin.blotters.show', $blotter)
                ->with('error', 'Set an initial meeting date and time before downloading the Patawag Complainant document.');
        }

        $blotter->loadMissing('respondentResident');
        $respondentName = $blotter->respondent_name ?: $blotter->respondentResident?->full_name;
        $processor = new TemplateProcessor($templatePath);
        $processor->setValue('respondent_full_name', trim($respondentName ?? ''));
        $processor->setValue('location', 'Bucandala 1, Imus, Barangay Hall');
        $processor->setValue('scheduled_at', $scheduledAt->format('F d, Y'));
        $processor->setValue('time', $scheduledAt->format('h:i A'));
        $processor->setValue('buwan', $scheduledAt->format('F'));
        $processor->setValue('araw', $scheduledAt->format('d'));

        $tempDirectory = storage_path('app/temp');
        File::ensureDirectoryExists($tempDirectory);
        $fileToken = Str::uuid()->toString();
        $filename = 'patawag_complainant_' . Str::slug($blotter->blotter_no) . '_' . $fileToken;
        $docxPath = $tempDirectory . DIRECTORY_SEPARATOR . $filename . '.docx';
        $pdfPath = $tempDirectory . DIRECTORY_SEPARATOR . $filename . '.pdf';
        $profilePath = $tempDirectory . DIRECTORY_SEPARATOR . 'lo-profile-' . $fileToken;
        $processor->saveAs($docxPath);

        $configuredBinary = config('services.libreoffice.binary');
        $binaryCandidates = array_filter([
            is_string($configuredBinary) ? $configuredBinary : null,
            PHP_OS_FAMILY === 'Windows' ? 'C:\\Program Files\\LibreOffice\\program\\soffice.com' : null,
            PHP_OS_FAMILY === 'Windows' ? 'C:\\Program Files\\LibreOffice\\program\\soffice.exe' : null,
            PHP_OS_FAMILY === 'Windows' ? 'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.com' : null,
            PHP_OS_FAMILY === 'Windows' ? 'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe' : null,
        ]);
        $binary = null;
        foreach ($binaryCandidates as $candidate) {
            if (is_file($candidate)) {
                $binary = $candidate;
                break;
            }

            $binary = (new ExecutableFinder())->find($candidate);
            if ($binary) {
                break;
            }
        }

        if (!$binary) {
            @unlink($docxPath);

            return redirect()
                ->route('admin.blotters.show', $blotter)
                ->with('error', 'LibreOffice is unavailable, so the Patawag document could not be converted to PDF. The blotter details were saved.');
        }

        $process = null;
        try {
            File::ensureDirectoryExists($profilePath);
            $profileUri = PHP_OS_FAMILY === 'Windows'
                ? 'file:///' . str_replace('\\', '/', $profilePath)
                : 'file://' . $profilePath;
            $process = new Process([
                $binary,
                '--headless',
                '--norestore',
                '--nolockcheck',
                '--nodefault',
                '--nofirststartwizard',
                '-env:UserInstallation=' . $profileUri,
                '--convert-to',
                'pdf:writer_pdf_Export',
                '--outdir',
                $tempDirectory,
                $docxPath,
            ], dirname($binary), array_filter([
                'HOME' => PHP_OS_FAMILY === 'Windows' ? null : $tempDirectory,
                'TEMP' => PHP_OS_FAMILY === 'Windows' ? $tempDirectory : null,
                'TMP' => PHP_OS_FAMILY === 'Windows' ? $tempDirectory : null,
            ]));
            $process->setTimeout(120);
            $process->run();

            if (!$process->isSuccessful() || !is_file($pdfPath) || filesize($pdfPath) === 0) {
                throw new \RuntimeException(sprintf(
                    'LibreOffice conversion failed (exit code: %s; stdout: %s; stderr: %s).',
                    $process->getExitCode() ?? 'unknown',
                    trim($process->getOutput()),
                    trim($process->getErrorOutput())
                ));
            }
        } catch (\Throwable $exception) {
            Log::error('Patawag Complainant PDF conversion failed.', [
                'blotter_id' => $blotter->id,
                'converter_binary' => $binary,
                'exit_code' => $process?->getExitCode(),
                'stdout' => $process?->getOutput(),
                'stderr' => $process?->getErrorOutput(),
                'exception' => $exception,
            ]);
            @unlink($pdfPath);

            return redirect()
                ->route('admin.blotters.show', $blotter)
                ->with('error', 'The Patawag DOCX could not be converted to PDF. Check the server logs and LibreOffice installation. The blotter details were saved.');
        } finally {
            @unlink($docxPath);
            File::deleteDirectory($profilePath);
        }

        return response()->download($pdfPath, $filename . '.pdf')->deleteFileAfterSend(true);
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
