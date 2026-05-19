<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseFile;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class CasePrintController extends Controller
{
    public function certToFileAction(CaseFile $case)
    {
        // load what we need
        $case->load([
            'blotter',
            'hearings',
            'blotter.complainantResident',
            'blotter.respondentResident',
        ]);

        $blotter = $case->blotter;

        // 1) template path (put your docx here)
        $templatePath = storage_path('app/public/document-templates/cases/cert_to_file_action.docx');

        if (!file_exists($templatePath)) {
            return back()->with('error', 'Certification template missing in storage.');
        }

        // helper: LASTNAME, FIRSTNAME M.
        $fmt = function ($r) {
            if (!$r) return '';
            $mi = $r->middle_name ? (' ' . strtoupper(substr($r->middle_name, 0, 1)) . '.') : '';
            return strtoupper($r->last_name . ', ' . $r->first_name . $mi);
        };

        $cRes = $blotter->complainantResident ?? null;
        $rRes = $blotter->respondentResident ?? null;

        $complainantName = $cRes ? $fmt($cRes) : strtoupper($blotter->complainant_name ?? '');
        $respondentName  = $rRes ? $fmt($rRes) : strtoupper($blotter->respondent_name ?? '');

        $complainantEmail   = $cRes?->email ?? ($blotter->complainant_email ?? '');
        $complainantContact = $cRes?->contact_no ?? ($blotter->complainant_contact ?? '');

        $respondentEmail   = $rRes?->email ?? ($blotter->respondent_email ?? '');
        $respondentContact = $rRes?->contact_no ?? ($blotter->respondent_contact ?? '');

        // 2) fill template placeholders
        $processor = new TemplateProcessor($templatePath);

        $processor->setValue('case_no', $case->case_no ?? '');
        $processor->setValue('blotter_no', $blotter->blotter_no ?? '');

        $processor->setValue('incident_type', $blotter->incident_type ?? '');
        $processor->setValue('incident_date', optional($blotter->incident_date)->format('F d, Y h:i A'));
        $processor->setValue('incident_location', $blotter->incident_location ?? '');

        $processor->setValue('complainant_name', $complainantName);
        $processor->setValue('complainant_email', $complainantEmail);
        $processor->setValue('complainant_contact', $complainantContact);

        $processor->setValue('respondent_name', $respondentName);
        $processor->setValue('respondent_email', $respondentEmail);
        $processor->setValue('respondent_contact', $respondentContact);

        $processor->setValue('hearing_count', (string) ($case->hearings?->count() ?? 0));
        $processor->setValue('resolution_summary', $case->resolution_summary ?? 'N/A');

        $processor->setValue('date_issued', now()->format('F d, Y'));

        // TODO later: pull from settings table
        $processor->setValue('barangay_captain', 'HON. JUAN DELA CRUZ');
        $processor->setValue('barangay_secretary', 'MARIA SANTOS');

        // 3) save temp & download
        $filename = 'CERT-TO-FILE-ACTION-' . ($case->case_no ?? Str::random(8)) . '.docx';
        $outPath = storage_path('app/temp/' . $filename);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        $processor->saveAs($outPath);

        return response()->download($outPath)->deleteFileAfterSend(true);
    }
}
