<?php

namespace Database\Seeders;

use App\Models\ProductoTodotex;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FixListovichBultoTextSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('seeders/files/Listovich.xlsx');
        $spreadsheet = IOFactory::load($file);

        $bultoByCode = [];
        $sheetOrder = 0;

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $sheetOrder++;
            $sheetTitle = trim((string) $sheet->getTitle());
            $highestRow = $sheet->getHighestRow();

            for ($row = 1; $row <= $highestRow; $row++) {
                $codigo = trim((string) $sheet->getCell('D' . $row)->getFormattedValue());

                if (!preg_match('/^\d+$/', $codigo)) {
                    continue;
                }

                $candidate = $this->resolveBultoFromSheet($sheetTitle, $sheet, $row);

                if ($candidate === null) {
                    continue;
                }

                $normalizedCandidate = ProductoTodotex::normalizeBulto($candidate);

                if ($normalizedCandidate === null) {
                    continue;
                }

                if (!isset($bultoByCode[$codigo][$normalizedCandidate])) {
                    $bultoByCode[$codigo][$normalizedCandidate] = [
                        'count' => 0,
                        'last_order' => 0,
                    ];
                }

                $bultoByCode[$codigo][$normalizedCandidate]['count']++;
                $bultoByCode[$codigo][$normalizedCandidate]['last_order'] = ($sheetOrder * 10000) + $row;
            }
        }

        $bestBultoByCode = [];
        foreach ($bultoByCode as $codigo => $options) {
            uasort($options, function (array $a, array $b) {
                if ($a['count'] !== $b['count']) {
                    return $b['count'] <=> $a['count'];
                }

                return $b['last_order'] <=> $a['last_order'];
            });

            $bestBultoByCode[$codigo] = array_key_first($options);
        }

        $updated = 0;

        ProductoTodotex::query()
            ->select(['id', 'codigo', 'bulto', 'bulto_cantidad'])
            ->chunkById(200, function ($productos) use ($bestBultoByCode, &$updated) {
                foreach ($productos as $producto) {
                    $codigo = trim((string) $producto->codigo);
                    $colorData = ProductoTodotex::deriveColorData($codigo);
                    $baseCode = $colorData['codigo_color'] ? substr($codigo, 0, -4) : $codigo;
                    $bulto = $bestBultoByCode[$baseCode] ?? null;

                    if ($bulto === null) {
                        continue;
                    }

                    $bultoCantidad = ProductoTodotex::deriveBultoCantidad($bulto);

                    if ($producto->bulto === $bulto && (int) $producto->bulto_cantidad === $bultoCantidad) {
                        continue;
                    }

                    $producto->forceFill([
                        'bulto' => $bulto,
                        'bulto_cantidad' => $bultoCantidad,
                    ])->save();

                    $updated++;
                }
            });

        $this->command->info("Productos actualizados con bulto textual: {$updated}");
    }

    private function resolveBultoFromSheet(string $sheetTitle, $sheet, int $row): ?string
    {
        $cell = $sheet->getCell('O' . $row);
        $rawValue = $cell->getValue();
        $rawText = trim((string) $rawValue);
        $formatCode = trim((string) $sheet->getStyle('O' . $row)->getNumberFormat()->getFormatCode());

        if ($rawText === '' || $rawValue === null) {
            if (mb_strtolower($sheetTitle) === 'industrial') {
                return 'Kg';
            }

            return null;
        }

        if (!is_numeric($rawValue)) {
            return $this->normalizeText($rawText);
        }

        $numberText = $this->stringifyNumericValue($rawValue);
        $unitText = $this->extractUnitFromFormat($formatCode);

        if ($unitText === null) {
            return $numberText;
        }

        return $this->normalizeText($numberText . ' ' . $unitText);
    }

    private function extractUnitFromFormat(string $formatCode): ?string
    {
        if ($formatCode === '') {
            return null;
        }

        if (!preg_match('/\[\$(.*?)\]/', $formatCode, $matches)) {
            return null;
        }

        $unit = trim(str_replace('\\', '', $matches[1]));

        return $unit !== '' ? $unit : null;
    }

    private function stringifyNumericValue($value): string
    {
        $numeric = (float) $value;

        if (floor($numeric) == $numeric) {
            return (string) (int) $numeric;
        }

        $text = rtrim(rtrim(number_format($numeric, 2, '.', ''), '0'), '.');

        return $text !== '' ? $text : '0';
    }

    private function normalizeText(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }
}
