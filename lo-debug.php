<?php
$binary = 'C:/Program Files/LibreOffice/program/soffice.com';
$docx = 'C:/Users/Yuan/Documents/projects/mis-bucandala/storage/app/temp';

echo "PHP binary: " . PHP_BINARY . PHP_EOL;
echo "PHP version: " . PHP_VERSION . PHP_EOL;
echo "Current directory: " . getcwd() . PHP_EOL;
echo "TEMP: " . sys_get_temp_dir() . PHP_EOL;
echo "PATH: " . getenv('PATH') . PHP_EOL;
echo "LibreOffice exists: " . (file_exists($binary) ? 'YES' : 'NO') . PHP_EOL;
echo "DOCX files currently in temp folder:" . PHP_EOL;

foreach (glob($docx . '/*.docx') as $file) {
    echo $file . ' | ' . filesize($file) . ' bytes | readable: ' . (is_readable($file) ? 'YES' : 'NO') . PHP_EOL;
}
