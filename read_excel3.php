<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('database/seeders/files/Tablocho Pablo.xlsx');
$sheet = $spreadsheet->getSheetByName('Newcost');

// Show rows around typical category/family headers - numbers in col A
// Also find all DISTINCT symbols in col A
$symbols = [];
$numbers = [];
$allColAValues = [];

echo "=== ROWS 50-120 ===" . PHP_EOL;
for ($row = 50; $row <= 120; $row++) {
    $a = $sheet->getCell('A' . $row)->getValue();
    $b = $sheet->getCell('B' . $row)->getValue();
    $c = $sheet->getCell('C' . $row)->getValue();
    $d = $sheet->getCell('D' . $row)->getValue();
    $e = $sheet->getCell('E' . $row)->getValue();
    $f = $sheet->getCell('F' . $row)->getValue();
    $h = $sheet->getCell('H' . $row)->getValue();
    if ($a !== null && $a !== '') {
        echo "Row $row: A=[" . $a . "] B=[" . $b . "] C=[" . trim($c) . "] D=[" . trim($d) . "] E=[" . $e . "] F=[" . $f . "] H=[" . $h . "]" . PHP_EOL;
    }
}

// Scan all rows up to 1034 to find all distinct symbols and number patterns
echo PHP_EOL . "=== SCANNING ALL ROWS 1-1034 FOR DISTINCT COL A VALUES ===" . PHP_EOL;
for ($row = 1; $row <= 1034; $row++) {
    $a = trim((string)$sheet->getCell('A' . $row)->getValue());
    if ($a !== '') {
        if (is_numeric($a)) {
            $numbers[$a] = ($numbers[$a] ?? 0) + 1;
        } else {
            $symbols[$a] = ($symbols[$a] ?? 0) + 1;
        }
    }
}

echo "SYMBOLS found:" . PHP_EOL;
foreach ($symbols as $sym => $cnt) {
    // Show hex codes
    $hex = bin2hex(mb_convert_encoding($sym, 'UTF-8'));
    echo "  [$sym] count=$cnt hex=$hex" . PHP_EOL;
}
echo "NUMBERS found:" . PHP_EOL;
foreach ($numbers as $num => $cnt) {
    echo "  [$num] count=$cnt" . PHP_EOL;
}
