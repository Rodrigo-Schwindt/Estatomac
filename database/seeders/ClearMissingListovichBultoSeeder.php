<?php

namespace Database\Seeders;

use App\Models\ProductoTodotex;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ClearMissingListovichBultoSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('seeders/files/Listovich.xlsx');
        $spreadsheet = IOFactory::load($file);

        $listovichCodes = [];
        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $highestRow = $sheet->getHighestRow();

            for ($row = 1; $row <= $highestRow; $row++) {
                $code = trim((string) $sheet->getCell('D' . $row)->getFormattedValue());

                if (preg_match('/^\d+$/', $code)) {
                    $listovichCodes[$code] = true;
                }
            }
        }

        $updated = 0;

        ProductoTodotex::query()
            ->select(['id', 'codigo', 'bulto', 'bulto_cantidad'])
            ->chunkById(200, function ($productos) use ($listovichCodes, &$updated) {
                foreach ($productos as $producto) {
                    $codigo = trim((string) $producto->codigo);
                    $colorData = ProductoTodotex::deriveColorData($codigo);
                    $baseCode = $colorData['codigo_color'] ? substr($codigo, 0, -4) : $codigo;

                    if ($baseCode !== '' && isset($listovichCodes[$baseCode])) {
                        continue;
                    }

                    if ($producto->bulto === null && $producto->bulto_cantidad === null) {
                        continue;
                    }

                    $producto->forceFill([
                        'bulto' => null,
                        'bulto_cantidad' => null,
                    ])->save();

                    $updated++;
                }
            });

        $this->command->info("Productos limpiados de bulto: {$updated}");
    }
}
