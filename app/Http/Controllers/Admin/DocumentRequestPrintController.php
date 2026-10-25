<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
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

        if ($documentRequest->statusEnum() !== DocumentRequestStatus::Completed) {
            return redirect()
                ->route('admin.document-requests.index')
                ->with('error', 'Only completed document requests can be downloaded.');
        }

        return $this->generateDocx($documentRequest);
    }

    public function releaseAndDownload(Request $request, DocumentRequest $documentRequest)
    {
        if (!in_array($documentRequest->statusEnum(), [DocumentRequestStatus::Pending, DocumentRequestStatus::Completed], true)) {
            return redirect()
                ->route('admin.document-requests.index')
                ->with('error', 'Only pending or completed document requests can be downloaded.');
        }

        $download = $this->generateDocx($documentRequest);
        if ($download instanceof RedirectResponse) {
            return $download;
        }

        DB::transaction(function () use ($request, $documentRequest) {
            $lockedRequest = DocumentRequest::query()
                ->lockForUpdate()
                ->findOrFail($documentRequest->id);

            $status = $lockedRequest->statusEnum();
            if (!in_array($status, [DocumentRequestStatus::Pending, DocumentRequestStatus::Completed], true)) {
                abort(409, 'Only pending or completed document requests can be downloaded.');
            }

            if ($status === DocumentRequestStatus::Pending) {
                $lockedRequest->update([
                    'status' => DocumentRequestStatus::ForPickup->value,
                    'released_by' => $request->user()->id,
                    'released_at' => now(),
                ]);
            }

            if ($status === DocumentRequestStatus::Pending) {
                $this->ensurePendingPayment($lockedRequest);
            } else {
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
            }
        });

        return $download;
    }

    public function printAndComplete(Request $request, DocumentRequest $documentRequest)
    {
        if (!in_array($documentRequest->statusEnum(), [DocumentRequestStatus::Pending, DocumentRequestStatus::ForPickup], true)) {
            return response()->json(['message' => 'Only pending or for-pickup requests can be printed and completed.'], 409);
        }

        $binary = config('services.libreoffice.binary');
        $binaryCandidates = array_filter([
    PHP_OS_FAMILY === 'Windows'
        ? 'C:\\Program Files\\LibreOffice\\program\\soffice.com'
        : null,
    is_string($binary) ? $binary : null,
    PHP_OS_FAMILY === 'Windows'
        ? 'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.com'
        : null,

]);
        $resolvedBinary = null;
        foreach ($binaryCandidates as $candidate) {
            if (is_file($candidate)) {
                $resolvedBinary = $candidate;
                break;
            }
            

            $resolvedBinary = (new ExecutableFinder())->find($candidate);
            if ($resolvedBinary) {
                break;
            }
        }
        Log::debug('Selected LibreOffice binary', [
    'configured_binary' => $binary,
    'resolved_binary' => $resolvedBinary,
]);
        if (!$resolvedBinary) {
            return response()->json([
                'message' => 'PDF conversion is unavailable because LibreOffice could not be found. Install LibreOffice on the server or set LIBREOFFICE_BINARY to the soffice executable path.',
            ], 503);
        }

        try {
            $pdf = $this->generateDocx($documentRequest, true, $resolvedBinary);
        } catch (\Throwable $exception) {
            Log::error('Document request PDF conversion failed.', [
                'document_request_id' => $documentRequest->id,
                'exception' => $exception,
            ]);

            return response()->json([
                'message' => 'The filled DOCX could not be converted to PDF. Check the server logs and LibreOffice installation, then try again.',
            ], 503);
        }
        if ($pdf instanceof RedirectResponse) {
            return response()->json(['message' => 'The document template is unavailable.'], 422);
        }

        DB::transaction(function () use ($request, $documentRequest) {
            $lockedRequest = DocumentRequest::query()->lockForUpdate()->findOrFail($documentRequest->id);
            if (!in_array($lockedRequest->statusEnum(), [DocumentRequestStatus::Pending, DocumentRequestStatus::ForPickup], true)) {
                abort(409, 'This request is no longer pending or for pickup.');
            }

            $lockedRequest->update([
                'status' => DocumentRequestStatus::ForPickup->value,
                'released_by' => $request->user()->id,
                'released_at' => now(),
            ]);

            $this->ensurePendingPayment($lockedRequest);
        });

        return $pdf;
    }

    public function setForPickup(DocumentRequest $documentRequest)
    {
        if ($documentRequest->statusEnum() !== DocumentRequestStatus::Pending) {
            return response()->json(['message' => 'Only pending requests can be set for pickup.'], 409);
        }

        DB::transaction(function () use ($documentRequest) {
            $lockedRequest = DocumentRequest::query()->lockForUpdate()->findOrFail($documentRequest->id);
            if ($lockedRequest->statusEnum() !== DocumentRequestStatus::Pending) {
                abort(409, 'This request is no longer pending.');
            }
            $lockedRequest->update(['status' => DocumentRequestStatus::ForPickup->value]);
            $this->ensurePendingPayment($lockedRequest);
        });

        return response()->json(['message' => 'Document request set for pickup.', 'success' => true]);
    }

    private function ensurePendingPayment(DocumentRequest $documentRequest): void
    {
        if ((float) $documentRequest->fee_amount <= 0) {
            return;
        }

        $payment = $documentRequest->payment()->lockForUpdate()->first();
        if ($payment) {
            $payment->update([
                'amount' => $documentRequest->fee_amount,
                'status' => 'pending',
                'paid_at' => now(),
            ]);

            return;
        }

        Payment::create([
            'document_request_id' => $documentRequest->id,
            'amount' => $documentRequest->fee_amount,
            'status' => 'pending',
            'paid_at' => now(),
        ]);
    }

    
private function generateDocx(
    DocumentRequest $documentRequest,
    bool $asPdf = false,
    ?string $converterBinary = null
) {
    file_put_contents(
    storage_path('logs/libreoffice-debug.log'),
    date('Y-m-d H:i:s') . " generateDocx reached\n",
    FILE_APPEND
);
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
        $age = (int) date('Y') - (int) date('Y', $birth);

        if (
            date('n', $birth) > date('n') ||
            (
                date('n', $birth) == date('n') &&
                date('j', $birth) > date('j')
            )
        ) {
            $age--;
        }
    }

    $processor = new TemplateProcessor($fullTemplate);

    // Common placeholders
    $processor->setValue(
        'full_name',
        trim($r->first_name . ' ' . $r->middle_name . ' ' . $r->last_name)
    );
    $processor->setValue('age', $age);
    $processor->setValue('address', $r->address_line ?? '');
    $processor->setValue('purpose', $documentRequest->purpose ?? 'N/A');
    $processor->setValue('control_no', $documentRequest->control_no ?? '');
    $processor->setValue('day', date('d'));
    $processor->setValue('month', date('F'));
    $processor->setValue('year', date('Y'));
    $processor->setValue(
        'date_of_birth',
        $r->birth_date ? date('F d, Y', strtotime($r->birth_date)) : ''
    );

    // Document-type-specific placeholders
    $docTypeName = strtolower(trim($documentRequest->documentType->name ?? ''));
    $cs = strtolower(trim($r->civil_status ?? ''));

    $isSingle = $cs === 'single';
    $isMarried = $cs === 'married';
    $isWidow = in_array($cs, ['widow', 'widowed', 'widower'], true);
    $isSeparated = $cs === 'separated';

    if (str_contains($docTypeName, 'unemployment')) {
        $processor->setValue('single_check', $isSingle ? '✓' : ' ');
        $processor->setValue('separated_check', $isSeparated ? '✓' : ' ');
        $processor->setValue('widow_check', $isWidow ? '✓' : ' ');
        $processor->setValue('married_check', $isMarried ? '✓' : ' ');
    } else {
        $processor->setValue('single_check', $isSingle ? '✓' : ' ');
        $processor->setValue('married_check', $isMarried ? '✓)' : ' ');
        $processor->setValue('widow_check', $isWidow ? '✓' : ' ');
        $processor->setValue('separated_check', $isSeparated ? '✓' : ' ');

        $processor->setValue(
            'residency_since',
            $documentRequest->created_at
                ? date('F d, Y', strtotime($documentRequest->created_at))
                : date('F d, Y')
        );
    }

    $processor->setValue('barangay_captain', 'HON. JUAN DELA CRUZ');
    $processor->setValue('barangay_secretary', 'MARIA SANTOS');

    $controlNo = Str::slug($documentRequest->control_no ?? Str::random(8));
    $fileToken = Str::uuid()->toString();

    $tempDirectory = storage_path('app/temp');

    if (!is_dir($tempDirectory) && !mkdir($tempDirectory, 0777, true) && !is_dir($tempDirectory)) {
        throw new \RuntimeException('Unable to create the temporary document directory.');
    }

    $filename = 'document_' . $fileToken . '.docx';
    $outPath = $tempDirectory . DIRECTORY_SEPARATOR . $filename;

    $processor->saveAs($outPath);

    if (!$asPdf) {
        return response()->download($outPath)->deleteFileAfterSend(true);
    }

    $pdfPath = $tempDirectory . DIRECTORY_SEPARATOR . 'document_' . $fileToken . '.pdf';

$outputDir = $tempDirectory;
$profilePath = $tempDirectory . DIRECTORY_SEPARATOR . 'lo-profile-' . $fileToken;

$process = null;
try {
    $binary = $converterBinary ?: config('services.libreoffice.binary');

    if (!$binary || !is_file($binary)) {
        throw new \RuntimeException(
            'LibreOffice executable not found: ' . ($binary ?: '(not configured)')
        );
    }


    if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
        throw new \RuntimeException('Unable to create temporary PDF output directory.');
    }

    if (!is_dir($profilePath) && !mkdir($profilePath, 0777, true) && !is_dir($profilePath)) {
        throw new \RuntimeException('Unable to create LibreOffice profile directory.');
    }

    $docxPath = realpath($outPath);

    if (!$docxPath || !is_file($docxPath) || !is_readable($docxPath)) {
        throw new \RuntimeException('Generated DOCX is missing or unreadable.');
    }

    $pdfPath = $outputDir . DIRECTORY_SEPARATOR
        . pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';

    if (is_file($pdfPath) && !unlink($pdfPath)) {
        throw new \RuntimeException('Unable to remove the previous temporary PDF.');
    }

    $profileUri = PHP_OS_FAMILY === 'Windows'
        ? 'file:///' . str_replace('\\', '/', rtrim($profilePath, '\\/'))
        : 'file://' . $profilePath;

    // Use clean path slashes for Windows arguments
    $winOutputDir = PHP_OS_FAMILY === 'Windows' ? str_replace('/', '\\', $outputDir) : $outputDir;
    $winDocxPath = PHP_OS_FAMILY === 'Windows' ? str_replace('/', '\\', $docxPath) : $docxPath;
    Log::debug('LibreOffice executable passed to Process', [
    'converter_binary' => $converterBinary,
    'binary' => $binary,
]);

\Log::debug('LibreOffice launch context', [
    'php_binary' => PHP_BINARY,
    'php_sapi' => PHP_SAPI,
    'current_user' => get_current_user(),
    'working_directory' => getcwd(),
    'home' => getenv('HOME'),
    'userprofile' => getenv('USERPROFILE'),
    'appdata' => getenv('APPDATA'),
    'localappdata' => getenv('LOCALAPPDATA'),
    'temp' => getenv('TEMP'),
    'tmp' => getenv('TMP'),
]);
$process = new Process(
    [
        $binary,
        '--headless',
        '--norestore',
        '--nolockcheck',
        '--nodefault',
        '--nofirststartwizard',
        '-env:UserInstallation=' . $profileUri,
        '--convert-to', 'pdf:writer_pdf_Export',
        '--outdir', $winOutputDir,
        $winDocxPath,
    ],
    null,
    // Web server users often have no HOME/APPDATA, which makes LibreOffice fail silently
    array_filter([
        'HOME' => PHP_OS_FAMILY === 'Windows' ? null : $tempDirectory,
        'TEMP' => PHP_OS_FAMILY === 'Windows' ? $tempDirectory : null,
        'TMP'  => PHP_OS_FAMILY === 'Windows' ? $tempDirectory : null,
    ])
);
$process->setTimeout(120); // first launch with a fresh profile is slow
$process->run();

Log::debug('LibreOffice conversion result', [
    'exit_code' => $process->getExitCode(),
    'stdout'    => $process->getOutput(),
    'stderr'    => $process->getErrorOutput(),
    'command'   => $process->getCommandLine(),
    'pdf_exists'=> is_file($pdfPath),
]);

if (!$process->isSuccessful() || !is_file($pdfPath)) {
    throw new \RuntimeException(
        'LibreOffice conversion failed. Exit code: ' . $process->getExitCode()
        . '. STDERR: ' . $process->getErrorOutput()
    );
}
} catch (\Throwable $e) {
    \Log::error('LibreOffice conversion failed', [
        'message' => $e->getMessage(),
        'exception_class' => get_class($e),
        'trace' => $e->getTraceAsString(),
    ]);

    throw $e;
} finally {
    if (is_file($outPath)) {
        // @unlink($outPath);
    }

    if (is_dir($profilePath)) {
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $profilePath,
                    \FilesystemIterator::SKIP_DOTS
                ),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $item) {
                $item->isDir()
                    ? @rmdir($item->getPathname())
                    : @unlink($item->getPathname());
            }

            @rmdir($profilePath);
        } catch (\Throwable $cleanupException) {
            Log::warning('Unable to fully clean LibreOffice profile directory.', [
                'profile_path' => $profilePath,
                'message' => $cleanupException->getMessage(),
            ]);
        }
    }
}

    return response()->file($pdfPath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="document_' . $controlNo . '.pdf"',
    ])->deleteFileAfterSend(true);
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
