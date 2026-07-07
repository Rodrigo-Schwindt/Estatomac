<?php

namespace Database\Seeders;

use App\Models\Vendedor;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class VendedorSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/files/06-05-26 Vendedores.xlsx');
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $imported = 0;
        $skipped = 0;

        foreach ($sheet->getRowIterator(2) as $row) {
            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = $cell->getValue();
            }

            // Columns: PersonalPK | Nombre | Telefono | Celular | Whatsapp | EMail | ActivoSN | Comision | OperaComo
            $codigoExterno = (int) ($cells[0] ?? 0);
            $nombre        = trim($cells[1] ?? '');
            $telefono      = $this->limpiarTexto($cells[2] ?? '');
            $celular       = $this->limpiarTexto($cells[3] ?? '');
            $whatsapp      = $this->limpiarTexto($cells[4] ?? '');
            $email         = $this->limpiarEmail($cells[5] ?? '');
            $activo        = strtoupper(trim($cells[6] ?? 'S')) === 'S';
            $comision      = is_numeric($cells[7] ?? null) ? (float) $cells[7] : 0;
            $operaComo     = $this->limpiarTexto($cells[8] ?? '');

            if (empty($nombre)) {
                $skipped++;
                continue;
            }

            Vendedor::updateOrCreate(
                ['codigo_externo' => $codigoExterno],
                [
                    'nombre'      => $nombre,
                    'telefono'    => $telefono,
                    'celular'     => $celular,
                    'whatsapp'    => $whatsapp,
                    'email'       => $email,
                    'activo'      => $activo,
                    'comision'    => $comision,
                    'opera_como'  => $operaComo,
                ]
            );

            $imported++;
        }

        $this->command->info("Vendedores: {$imported} importados, {$skipped} omitidos.");
    }

    private function limpiarTexto(?string $valor): ?string
    {
        if ($valor === null) return null;
        $valor = trim($valor);
        return ($valor === '' || $valor === '-' || $valor === '+54') ? null : $valor;
    }

    private function limpiarEmail(?string $valor): ?string
    {
        if ($valor === null) return null;
        $valor = trim($valor);
        if ($valor === '' || $valor === '-' || !str_contains($valor, '@')) {
            return null;
        }
        return $valor;
    }
}
