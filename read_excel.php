<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('database/seeders/files/Tablocho Pablo.xlsx');
$sheets = $spreadsheet->getSheetNames();
echo "Sheets: " . implode(', ', $sheets) . PHP_EOL;
echo "Total sheets: " . count($sheets) . PHP_EOL;
