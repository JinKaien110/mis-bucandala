<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

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

        $tempDirectory = storage_path('app/temp');
        File::ensureDirectoryExists($tempDirectory);
        $fileToken = Str::uuid()->toString();
        $filename = 'CERT-TO-FILE-ACTION-' . Str::slug($case->case_no ?? Str::random(8)) . '-' . $fileToken;
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
            File::delete($docxPath);

            return back()->with('error', 'LibreOffice is unavailable, so the certification could not be converted to PDF.');
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
            Log::error('Case certification PDF conversion failed.', [
                'case_id' => $case->id,
                'converter_binary' => $binary,
                'exit_code' => $process?->getExitCode(),
                'stdout' => $process?->getOutput(),
                'stderr' => $process?->getErrorOutput(),
                'exception' => $exception,
            ]);
            File::delete($pdfPath);

            return back()->with('error', 'The case certification could not be converted to PDF. Check the server logs and LibreOffice installation.');
        } finally {
            File::delete($docxPath);
            File::deleteDirectory($profilePath);
        }

        return response()->download($pdfPath, $filename . '.pdf')->deleteFileAfterSend(true);
    }
}
