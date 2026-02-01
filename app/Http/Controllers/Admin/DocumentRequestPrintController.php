<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentRequestPrintController extends Controller
{
    public function download(DocumentRequest $documentRequest)
    {
        // load relations (make sure these exist)
        $documentRequest->load(['resident', 'documentType']);

        // 1) must have template
        $templatePath = $documentRequest->documentType->template_path ?? null;
        if (!$templatePath) {
            return back()->with('error', 'No template uploaded for this document type.');
        }

        $fullTemplate = storage_path('app/public/' . $templatePath);
        if (!file_exists($fullTemplate)) {
            return back()->with('error', 'Template file missing in storage.');
        }

        // 2) fill template
        $r = $documentRequest->resident;

        $processor = new TemplateProcessor($fullTemplate);

        // IMPORTANT: your placeholders in Word should be like {{full_name}}
        $processor->setValue('full_name', trim($r->first_name.' '.$r->middle_name.' '.$r->last_name));
        $processor->setValue('address', $r->address_line ?? '');
        $processor->setValue('birth_date', $r->birth_date ? date('F d, Y', strtotime($r->birth_date)) : '');
        $processor->setValue('civil_status', $r->civil_status ?? '');
        $processor->setValue('sex', $r->sex ?? '');

        $processor->setValue('purpose', $documentRequest->purpose ?? 'N/A');
        $processor->setValue('control_no', $documentRequest->control_no ?? '');
        $processor->setValue('date_issued', now()->format('F d, Y'));
        $processor->setValue('issued_day', now()->format('d'));
        $processor->setValue('issued_month', now()->format('F'));
        $processor->setValue('issued_year', now()->format('Y'));
        $processor->setValue('barangay_captain', 'HON. JUAN DELA CRUZ');
        $processor->setValue('barangay_secretary', 'MARIA SANTOS');

        // 3) save temp output docx
        $filename = 'document_' . ($documentRequest->control_no ?? Str::random(8)) . '.docx';
        $outPath = storage_path('app/temp/' . $filename);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        $processor->saveAs($outPath);

        // optional: update status
        // $documentRequest->update(['status' => 'generated']);

        // 4) return download
        return response()->download($outPath)->deleteFileAfterSend(true);
    }
}
