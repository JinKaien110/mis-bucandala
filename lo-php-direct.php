<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Process\Process;

$binary = 'C:/Program Files/LibreOffice/program/soffice.com';
$docx = 'C:/Users/Yuan/Documents/projects/mis-bucandala/storage/app/temp/document_0e56e5c5-5e75-49cf-81df-1ebd403d8ccf.docx';
$output = sys_get_temp_dir() . '/lo-php-direct-output';
$profile = sys_get_temp_dir() . '/lo-php-direct-profile';

if (!is_dir($output)) mkdir($output, 0777, true);

$profileUri = 'file:///' . str_replace('\\', '/', $profile);

$process = new Process([
    $binary,
    '--headless',
    '-env:UserInstallation=' . $profileUri,
    '--convert-to',
    'pdf:writer_pdf_Export',
    '--outdir',
    $output,
    $docx,
]);

$process->setWorkingDirectory(dirname($docx));
$process->setTimeout(120);
$process->run();

echo "Exit code: " . $process->getExitCode() . PHP_EOL;
echo "STDOUT: " . $process->getOutput() . PHP_EOL;
echo "STDERR: " . $process->getErrorOutput() . PHP_EOL;
echo "PDF exists: " . (file_exists($output . '/document_0e56e5c5-5e75-49cf-81df-1ebd403d8ccf.pdf') ? 'YES' : 'NO') . PHP_EOL;
