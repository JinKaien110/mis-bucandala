<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Blotter;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Payment;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PortalController extends Controller
{
    private const DOCUMENT_TYPES = [
        'Barangay Certificate of Residency',
        'Barangay Clearance',
    ];

    private const PURPOSES = [
        'Employment',
        'School or Educational Requirement',
        'Scholarship Application',
        'Financial Assistance',
        'Medical Assistance',
        'Business Requirement',
        'Government Transaction',
        'Personal Requirement',
        'Others',
    ];

    public function createBlotter(Request $request)
    {
        $this->residentFor($request);

        return view('resident.blotters.create');
    }

    public function storeBlotter(Request $request)
    {
        $incidentTypes = array_column(
            array_merge(...array_values(config('blotter.incident_types'))),
            'value'
        );

        $validated = $request->validate([
            'incident_date' => ['required', 'date'],
            'incident_type' => ['required', 'string', 'max:255', Rule::in($incidentTypes)],
            'incident_location' => ['required', 'string', 'max:255'],
            'narrative' => ['required', 'string'],
            'respondent_name' => ['nullable', 'string', 'max:255'],
            'respondent_contact' => ['nullable', 'string', 'max:50'],
            'respondent_email' => ['nullable', 'email', 'max:255'],
            'respondent_address_line' => ['nullable', 'string', 'max:255'],
        ]);

        $resident = $this->residentFor($request);
        $complainantName = $this->residentFullName($resident);

        $blotter = DB::transaction(function () use ($validated, $resident, $complainantName, $request) {
            $year = now()->year;
            $last = Blotter::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $next = 1;
            if ($last && preg_match('/^BLT\-'.$year.'\-(\d{6})$/', $last->blotter_no, $matches)) {
                $next = ((int) $matches[1]) + 1;
            }

            return Blotter::create(array_merge($validated, [
                'blotter_no' => 'BLT-'.$year.'-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT),
                'complainant_resident_id' => $resident->id,
                'complainant_name' => $complainantName,
                'complainant_contact' => $resident->contact_no,
                'complainant_email' => $resident->email,
                'recorded_by' => $request->user()->id,
                'status' => 'filed',
            ]));
        });

        return redirect()
            ->route('resident.blotters.show', $blotter)
            ->with('success', "Your report was submitted. Reference number: {$blotter->blotter_no}.");
    }

    public function showBlotter(Request $request, Blotter $blotter)
    {
        $resident = $this->residentFor($request);

        abort_unless($blotter->complainant_resident_id === $resident->id, 404);

        $blotter->load(['case.hearings']);

        return view('resident.blotters.show', compact('blotter'));
    }

    public function cases(Request $request)
    {
        $resident = $this->residentFor($request);
        $blotters = Blotter::query()
            ->notArchived()
            ->where('complainant_resident_id', $resident->id)
            ->with(['case.hearings'])
            ->orderByDesc('incident_date')
            ->get();

        return view('resident.cases.index', compact('blotters'));
    }

    public function documentRequests(Request $request)
    {
        $resident = $this->residentFor($request);
        $documentTypes = DocumentType::query()
            ->whereIn('name', self::DOCUMENT_TYPES)
            ->where('status', 'active')
            ->notArchived()
            ->orderBy('name')
            ->get(['id', 'name', 'fee']);

        $requests = DocumentRequest::query()
            ->where('resident_id', $resident->id)
            ->with('documentType:id,name')
            ->orderByDesc('created_at')
            ->get();

        return view('resident.document-requests.index', compact('documentTypes', 'requests'));
    }

    public function storeDocumentRequest(Request $request)
    {
        $validated = $request->validate([
            'document_type_id' => [
                'required',
                'integer',
                Rule::exists('document_types', 'id')->where(function ($query) {
                    $query->where('status', 'active')
                        ->whereNull('archived_at')
                        ->whereIn('name', self::DOCUMENT_TYPES);
                }),
            ],
            'purpose_type' => ['required', Rule::in(self::PURPOSES)],
            'other_purpose' => ['nullable', 'required_if:purpose_type,Others', 'string', 'max:255'],
        ]);

        $resident = $this->residentFor($request);
        $documentType = DocumentType::query()
            ->whereKey($validated['document_type_id'])
            ->whereIn('name', self::DOCUMENT_TYPES)
            ->where('status', 'active')
            ->notArchived()
            ->firstOrFail();

        $purpose = $validated['purpose_type'] === 'Others'
            ? 'Others: '.trim($validated['other_purpose'])
            : $validated['purpose_type'];
        $fee = (float) ($documentType->fee ?? 0);

        $documentRequest = DB::transaction(function () use ($request, $resident, $documentType, $purpose, $fee) {
            $lastId = DocumentRequest::query()
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('id') ?? 0;
            $controlNo = 'BRGY-'.now()->format('Y').'-'.str_pad((string) ($lastId + 1), 6, '0', STR_PAD_LEFT);

            $documentRequest = DocumentRequest::create([
                'control_no' => $controlNo,
                'resident_id' => $resident->id,
                'document_type_id' => $documentType->id,
                'purpose' => $purpose,
                'fee_amount' => $fee,
                'status' => 'pending',
                'requested_by' => $request->user()->id,
                'requested_via' => 'resident',
            ]);

            if ($fee > 0) {
                Payment::create([
                    'document_request_id' => $documentRequest->id,
                    'amount' => $fee,
                    'status' => 'pending',
                ]);
            }

            return $documentRequest;
        });

        $feeLabel = $fee > 0 ? number_format($fee, 2) : 'FREE';

        return redirect()
            ->route('resident.document-requests')
            ->with('success', "Your request was submitted. Control No: {$documentRequest->control_no} | Fee: {$feeLabel} | Please claim your document request at the barangay hall with your ID and pay accordingly");
    }

    private function residentFor(Request $request): Resident
    {
        $resident = $request->user()->resident;
        abort_unless($resident, 403, 'A resident profile is required to use this service.');

        return $resident;
    }

    private function residentFullName(Resident $resident): string
    {
        return trim(implode(' ', array_filter([
            $resident->first_name,
            $resident->middle_name,
            $resident->last_name,
            $resident->suffix,
        ])));
    }
}
