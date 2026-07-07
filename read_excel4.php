<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('database/seeders/files/Tablocho Pablo.xlsx');
$sheet = $spreadsheet->getSheetByName('Newcost');

echo "=== ALL HEADER ROWS (col A = number) up to row 1034 ===" . PHP_EOL;
for ($row = 1; $row <= 1034; $row++) {
    $a = trim((string)$sheet->getCell('A' . $row)->getValue());
    if ($a !== '' && is_numeric($a)) {
        $b = trim((string)$sheet->getCell('B' . $row)->getValue());
        $c = trim((string)$sheet->getCell('C' . $row)->getValue());
        $d = trim((string)$sheet->getCell('D' . $row)->getValue());
        echo "Row $row: A=$a | B=[$b] | C=[$c]" . PHP_EOL;
    }
}

echo PHP_EOL . "=== ROWS 1030-1040 (near the end) ===" . PHP_EOL;
for ($row = 1025; $row <= 1040; $row++) {
    $a = $sheet->getCell('A' . $row)->getValue();
    $b = $sheet->getCell('B' . $row)->getValue();
    $c = $sheet->getCell('C' . $row)->getValue();
    $d = $sheet->getCell('D' . $row)->getValue();
    $e = $sheet->getCell('E' . $row)->getValue();
    $f = $sheet->getCell('F' . $row)->getValue();
    if ($a !== null || $b !== null) {
        echo "Row $row: A=[$a] B=[$b] C=[$c] D=[$d] E=[$e] F=[$f]" . PHP_EOL;
    }
}
