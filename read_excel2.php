<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('database/seeders/files/Tablocho Pablo.xlsx');

// Read SIMBOLOGIA sheet
echo "=== SIMBOLOGIA SHEET ===" . PHP_EOL;
$sheet = $spreadsheet->getSheetByName('SIMBOLOGIA');
if ($sheet) {
    $maxRow = $sheet->getHighestRow();
    $maxCol = $sheet->getHighestColumn();
    echo "Rows: $maxRow, MaxCol: $maxCol" . PHP_EOL;
    for ($row = 1; $row <= min($maxRow, 30); $row++) {
        $a = $sheet->getCell('A' . $row)->getValue();
        $b = $sheet->getCell('B' . $row)->getValue();
        $c = $sheet->getCell('C' . $row)->getValue();
        $d = $sheet->getCell('D' . $row)->getValue();
        echo "Row $row: A=[" . $a . "] B=[" . $b . "] C=[" . $c . "] D=[" . $d . "]" . PHP_EOL;
    }
}

echo PHP_EOL . "=== NEWCOST - First 50 rows ===" . PHP_EOL;
$sheet = $spreadsheet->getSheetByName('Newcost');
$maxRow = $sheet->getHighestRow();
echo "Total rows in Newcost: $maxRow" . PHP_EOL;
for ($row = 1; $row <= 50; $row++) {
    $a = $sheet->getCell('A' . $row)->getValue();
    $b = $sheet->getCell('B' . $row)->getValue();
    $c = $sheet->getCell('C' . $row)->getValue();
    $d = $sheet->getCell('D' . $row)->getValue();
    $e = $sheet->getCell('E' . $row)->getValue();
    $f = $sheet->getCell('F' . $row)->getValue();
    $g = $sheet->getCell('G' . $row)->getValue();
    $h = $sheet->getCell('H' . $row)->getValue();
    if ($a !== null || $b !== null || $c !== null) {
        echo "Row $row: A=[" . $a . "] B=[" . $b . "] C=[" . $c . "] D=[" . $d . "] E=[" . $e . "] F=[" . $f . "] G=[" . $g . "] H=[" . $h . "]" . PHP_EOL;
    }
}
