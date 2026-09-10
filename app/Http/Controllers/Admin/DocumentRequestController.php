<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Payment;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;

class DocumentRequestController extends Controller
{
    public function index()
    {
        return view('admin.document-management.request');
    }

     public function store(Request $request)
    {
        $data = $request->validate([
            'resident_id' => ['required','exists:residents,id'],
            'document_type_id' => ['required','exists:document_types,id'],
            'purpose' => ['nullable','string','max:255'],
            'remarks' => ['nullable','string','max:255'],
        ]);

        $docType = DocumentType::query()
            ->where('id', $data['document_type_id'])
            ->where('status', 'active')
            ->whereNull('archived_at') // exclude archived templates
            ->firstOrFail();

        $controlNo = $this->generateControlNo();

        // ✅ fee always numeric (DECIMAL). 0.00 means FREE.
        $fee = (float) ($docType->fee ?? 0);

        $req = DocumentRequest::create([
            'control_no' => $controlNo,
            'resident_id' => $data['resident_id'],
            'document_type_id' => $data['document_type_id'],
            // Ensure consistency: selected document type fee must match stored request fee_amount
            'fee_amount' => $fee,
            'purpose' => $data['purpose'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'status' => 'released',
        ]);


        // Auto-create payment record if there's a fee
        if ($fee > 0) {
            Payment::create([
                'document_request_id' => $req->id,
                'amount' => $fee,
                'status' => 'success',
                'paid_at' => now(),
            ]);
        }


        $feeLabel = $fee > 0 ? number_format($fee, 2) : 'FREE';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "Document request created. Control No: {$req->control_no} | Fee: {$feeLabel}"]);
        }

        return back()->with('success', "Document request created. Control No: {$req->control_no} | Fee: {$feeLabel}");
    }

    public function download(DocumentRequest $documentRequest)
    {

        $documentRequest->load(['resident', 'documentType']);
        $templatePath = $documentRequest->documentType->template_path ?? null;
        if (!$templatePath) {
            return back()->with('error', 'No template uploaded for this document type.');
        }
        $fullTemplate = storage_path('app/public/' . $templatePath);
        if (!file_exists($fullTemplate)) {
            return back()->with('error', 'Template file missing in storage.');
        }

        $r = $documentRequest->resident;
        $age = '';
        if ($r->birth_date) {
            $birth = strtotime($r->birth_date);
            $age = date('Y') - date('Y', $birth);
            if (date('n', $birth) > date('n') || (date('n', $birth) == date('n') && date('j', $birth) > date('j'))) {
                $age--;
            }
        }

        $processor = new TemplateProcessor($fullTemplate);

        // Common placeholders for all certificate types
        $processor->setValue('full_name', trim($r->first_name.' '.$r->middle_name.' '.$r->last_name));
        $processor->setValue('age', $age);
        $processor->setValue('address', $r->address_line ?? '');
        $processor->setValue('purpose', $documentRequest->purpose ?? 'N/A');
        $processor->setValue('control_no', $documentRequest->control_no ?? '');
        $processor->setValue('day', date('d'));
        $processor->setValue('month', date('F'));
        $processor->setValue('year', date('Y'));

        // Document-type specific placeholders
        $docTypeName = strtolower($documentRequest->documentType->name ?? '');
        if (str_contains($docTypeName, 'unemployment')) {
            $cs = strtolower(trim($r->civil_status ?? ''));
            $processor->setValue('single_check',    $cs === 'single'    ? '✓' : ' ');
            $processor->setValue('married_check',   $cs === 'married'   ? '✓' : ' ');
            $processor->setValue('widow_check',     in_array($cs, ['widow','widowed']) ? '✓' : ' ');
            $processor->setValue('separated_check', $cs === 'separated' ? '✓' : ' ');
        } else {
            $cs = strtolower(trim($r->civil_status ?? ''));
            $processor->setValue('is_single',    $cs === 'single'    ? 'Single(w✓)'   : 'Single( )');
            $processor->setValue('is_married',   $cs === 'married'   ? 'Married(✓)'  : 'Married( )');
            $processor->setValue('is_widow',     in_array($cs, ['widow','widowed']) ? 'Widow/Widower(✓)' : 'Widow/Widower( )');
            $processor->setValue('is_separated', $cs === 'separated' ? 'Separated(✓)' : 'Separated( )');
            $processor->setValue('residency_since', $documentRequest->created_at ? date('F d, Y', strtotime($documentRequest->created_at)) : date('F d, Y'));
        }

        $processor->setValue('barangay_captain', 'HON. JUAN DELA CRUZ');
        $processor->setValue('barangay_secretary', 'MARIA SANTOS');

        $filename = 'document_' . ($documentRequest->control_no ?? Str::random(8)) . '.docx';
        $outPath = storage_path('app/temp/' . $filename);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }
        $processor->saveAs($outPath);

        return response()->download($outPath)->deleteFileAfterSend(true);
    }

    /**
     * Walk-in: usually you only need to edit purpose/remarks.
     * Removed status validation.
     */
     public function update(Request $request, DocumentRequest $documentRequest)
    {
        $data = $request->validate([
            'purpose' => ['nullable','string','max:255'],
            'remarks' => ['nullable','string','max:255'],
        ]);

        $documentRequest->update($data);

        return back()->with('success', "Document request updated (Control No: {$documentRequest->control_no}).");
    }

    /**
     * Optional: If status is not applicable, you can REMOVE this method + its route.
     * If you keep it, maybe toggle "cancelled" isn't needed in walk-in.
     */
    public function toggle(DocumentRequest $documentRequest)
    {
        return back()->with('success', "Status toggle disabled for walk-in requests.");
    }

   private function generateControlNo(): string
    {
        $year = now()->format('Y');
        $last = DocumentRequest::whereYear('created_at', $year)->orderBy('id','desc')->value('id') ?? 0;
        $seq = str_pad((string)($last + 1), 6, '0', STR_PAD_LEFT);
        return "BRGY-{$year}-{$seq}";
    }

     public function apiIndex(Request $request)
    {
        $q = $request->query('q');

        $items = DocumentRequest::query()
            ->notArchived()
            ->with([
                'resident:id,first_name,middle_name,last_name,address_line',
                'documentType:id,name,fee'
            ])
            ->when($q, function ($query) use ($q) {
                $query->where('control_no', 'like', "%{$q}%")
                    ->orWhereHas('resident', function ($qq) use ($q) {
                        $qq->where('first_name','like',"%{$q}%")
                           ->orWhere('last_name','like',"%{$q}%")
                           ->orWhere('address_line','like',"%{$q}%");
                    })
                    ->orWhereHas('documentType', function ($qq) use ($q) {
                        $qq->where('name','like',"%{$q}%");
                    });
            })
            ->orderBy('id','desc')
            ->get();

        return response()->json(['document_requests' => $items]);
    }

    public function apiShow(DocumentRequest $documentRequest)
    {
        $documentRequest->load('resident','documentType');
        return response()->json(['document_request' => $documentRequest]);
    }

    public function residentOptions()
    {
        $residents = \App\Models\Resident::query()
            ->orderBy('last_name')
            ->get(['id','first_name','middle_name','last_name','address_line']);

        return response()->json(['residents' => $residents]);
    }

    public function documentTypeOptions()
    {
        $types = \App\Models\DocumentType::query()
            ->where('status', 'active')
            ->notArchived()
            ->orderBy('name')
            ->get(['id','name','fee']);

        return response()->json(['document_types' => $types]);
    }

    public function destroy(DocumentRequest $documentRequest)
    {
        $documentRequest->archive();
        
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Document request archived.']);
        }
        return back()->with('success', 'Document request archived.');
    }

    public function restore(DocumentRequest $documentRequest)
    {
        $documentRequest->restore();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Document request restored.']);
        }
        return back()->with('success', 'Document request restored.');
    }
}
