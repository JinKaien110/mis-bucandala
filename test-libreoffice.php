<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Process\Process;

$docx = __DIR__ . '/storage/app/temp/document_0e430723-937f-4468-8647-696e27639f2e.docx';
$binary = 'C:/Program Files/LibreOffice/program/soffice.com';
$output = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'lo-symfony-test';

if (!is_dir($output)) {
    mkdir($output, 0777, true);
}

$process = new Process([
    $binary,
    '--headless',
    '--convert-to',
    'pdf:writer_pdf_Export',
    '--outdir',
    $output,
    realpath($docx),
]);

$process->setTimeout(120);
$process->run();

var_dump([
    'docx_exists' => file_exists($docx),
    'docx_readable' => is_readable($docx),
    'command' => $process->getCommandLine(),
    'exit_code' => $process->getExitCode(),
    'stdout' => $process->getOutput(),
    'stderr' => $process->getErrorOutput(),
    'pdf_exists' => file_exists(
        $output . DIRECTORY_SEPARATOR . basename($docx, '.docx') . '.pdf'
    ),
]);