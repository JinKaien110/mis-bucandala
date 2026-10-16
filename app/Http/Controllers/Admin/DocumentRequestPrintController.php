<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class DocumentRequestPrintController extends Controller
{
    public function patawagComplainant(DocumentRequest $documentRequest)
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

        $documentRequest->load(['cases.caseFile', 'cases.caseFile.blotter']);
        $r = $documentRequest->resident;
        $caseHearings = $documentRequest->cases;
        $ch = $caseHearings->first();

        $complainantFullName = '';
        $location = '';
        $scheduledAt = null;
        if ($ch && $ch->caseFile && $ch->caseFile->blotter) {
            $blotter = $ch->caseFile->blotter;
            $complainantFullName = $blotter->complainant_name ?? '';
            $location = $ch->location ?? '';
            $scheduledAt = $ch->scheduled_at;
        }

        $time = $araw = $buwan = '';
        if ($scheduledAt) {
            $time = $scheduledAt->format('h:i A');
            $araw = $scheduledAt->format('d');
            $buwan = $scheduledAt->format('F');
        }

        $values = [
            'complainant_full_name' => $complainantFullName,
            'location' => $location,
            'scheduled_at' => $scheduledAt ? $scheduledAt->format('F d, Y') : '',
            'time' => $time,
            'araw' => $araw,
            'buwan' => $buwan,
        ];

        $pdf = Pdf::loadView('admin.documents.templates.default', $values);
        return $pdf->download('patawag_complainant_' . ($documentRequest->control_no ?? Str::random(8)) . '.pdf');
    }

    public function download(DocumentRequest $documentRequest)
    {
        if ($documentRequest->status === 'pending') {
            return redirect()
                ->route('admin.document-requests.index')
                ->with('error', 'Confirm the document release from the request list before downloading.');
        }

        if ($documentRequest->status !== 'released') {
            return redirect()
                ->route('admin.document-requests.index')
                ->with('error', 'Only released document requests can be downloaded.');
        }

        return $this->generateDocx($documentRequest);
    }

    public function releaseAndDownload(Request $request, DocumentRequest $documentRequest)
    {
        if (!in_array($documentRequest->status, ['pending', 'released'], true)) {
            return redirect()
                ->route('admin.document-requests.index')
                ->with('error', 'Only pending or released document requests can be downloaded.');
        }

        $download = $this->generateDocx($documentRequest);
        if ($download instanceof RedirectResponse) {
            return $download;
        }

        DB::transaction(function () use ($request, $documentRequest) {
            $lockedRequest = DocumentRequest::query()
                ->lockForUpdate()
                ->findOrFail($documentRequest->id);

            if (!in_array($lockedRequest->status, ['pending', 'released'], true)) {
                abort(409, 'Only pending or released document requests can be downloaded.');
            }

            if ($lockedRequest->status === 'pending') {
                $lockedRequest->update([
                    'status' => 'released',
                    'released_by' => $request->user()->id,
                    'released_at' => now(),
                ]);
            }

            $payment = $lockedRequest->payment()->lockForUpdate()->first();
            if ($payment) {
                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                ]);
            } elseif ((float) $lockedRequest->fee_amount > 0) {
                Payment::create([
                    'document_request_id' => $lockedRequest->id,
                    'amount' => $lockedRequest->fee_amount,
                    'status' => 'success',
                    'paid_at' => now(),
                ]);
            }
        });

        return $download;
    }

    private function generateDocx(DocumentRequest $documentRequest)
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
        $processor->setValue('date_of_birth', $r->birth_date ? date('F d, Y', strtotime($r->birth_date)) : '');

        // Document-type specific placeholders
       $docTypeName = strtolower(trim($documentRequest->documentType->name ?? ''));

if (str_contains($docTypeName, 'unemployment')) {

    $cs = strtolower(trim($r->civil_status ?? ''));

    // Normalize possible database variations
    $isSingle = $cs === 'single';
    $isMarried = $cs === 'married';
    $isWidow = in_array($cs, ['widow', 'widowed', 'widower']);
    $isSeparated = $cs === 'separated';
    

    $processor->setValue(
        'single_check',
        $isSingle ? '✓' : ' '
    );

    $processor->setValue(
        'separated_check',
        $isSeparated ? '✓' : ' '
    );

    $processor->setValue(
        'widow_check',
        $isWidow ? '✓' : ' '
    );

    $processor->setValue(
        'married_check',
        $isMarried ? '✓' : ' '
    );

} else {

    $cs = strtolower(trim($r->civil_status ?? ''));
    
    $isSingle = $cs === 'single';
    $isMarried = $cs === 'married';
    $isWidow = in_array($cs, ['widow', 'widowed', 'widower']);
    $isSeparated = $cs === 'separated';

    $processor->setValue(
        'single_check',
        $cs === 'single' ? '✓' : ' '
    );

    $processor->setValue(
        'married_check',
        $cs === 'married' ? '✓)' : ' '
    );

    $processor->setValue(
        'widow_check',
        in_array($cs, ['widow', 'widowed', 'widower'])
            ? '✓'
            : ' '
    );

    $processor->setValue(
        'separated_check',
        $cs === 'separated'
            ? '✓'
            : ' '
    );

    $processor->setValue(
        'residency_since',
        $documentRequest->created_at
            ? date('F d, Y', strtotime($documentRequest->created_at))
            : date('F d, Y')
    );
    
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

    public function print(DocumentRequest $documentRequest)
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
            $processor->setValue('is_single',    $cs === 'single'    ? 'Single(✓)'   : 'Single( )');
            $processor->setValue('is_married',   $cs === 'married'   ? 'Married(✓)'  : 'Married( )');
            $processor->setValue('is_widow',     in_array($cs, ['widow','widowed']) ? 'Widow/Widower(✓)' : 'Widow/Widower( )');
            $processor->setValue('is_separated', $cs === 'separated' ? 'Separated(✓)' : 'Separated( )');
            $processor->setValue('residency_since', $documentRequest->created_at ? date('F d, Y', strtotime($documentRequest->created_at)) : date('F d, Y'));
        }

        $processor->setValue('barangay_captain', 'HON. JUAN DELA CRUZ');
        $processor->setValue('barangay_secretary', 'MARIA SANTOS');

        $filename = 'certificate_' . ($documentRequest->control_no ?? Str::random(8)) . '.docx';
        $outPath = storage_path('app/temp/' . $filename);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }
        $processor->saveAs($outPath);

        // Return Word file for inline viewing in browser (opens in Office Online or downloads)
        return response()->file($outPath, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ])->deleteFileAfterSend(true);
    }
}
