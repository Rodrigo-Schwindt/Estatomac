<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Simbolo;
use App\Models\Familia;
use App\Models\CategoriaTodotex;
use App\Models\ProductoTodotex;

class NewcostSeeder extends Seeder
{
    /**
     * Mapeo símbolo (carácter Unicode) → nombre en SIMBOLOGIA
     * Orden respeta los IDs 1-13 de la hoja SIMBOLOGIA
     */
    private array $symbolNames = [
        '♣' => 'TREBOL',
        '♦' => 'DIAMANTE',
        '✽' => 'ESTRELLA ROJA',
        'ß' => 'BETA',
        '★' => 'ESTRELLA AMARILLA',
        '☸' => 'RUEDA',
        '⨷' => 'EQUIS',
        'm' => 'M',
        'e' => 'E',
        '♔' => 'CORONA',
        'ℕ' => 'N',
        '𝔽' => 'F',
        'ℙ' => 'P',
    ];

    public function run(): void
    {
        $file = database_path('seeders/files/Tablocho Pablo.xlsx');

        $this->command->info('Cargando Excel...');
        $spreadsheet = IOFactory::load($file);

        // 1. Crear/verificar los 13 símbolos
        $simboloIdMap = $this->seedSimbolos($spreadsheet);
        $this->command->info('Símbolos listos: ' . count($simboloIdMap));

        // 2. Importar productos de la hoja Newcost
        $sheet = $spreadsheet->getSheetByName('Newcost');
        $this->importarProductos($sheet, $simboloIdMap);
    }

    private function seedSimbolos($spreadsheet): array
    {
        $sheet = $spreadsheet->getSheetByName('SIMBOLOGIA');

        // Leer nombres de la hoja SIMBOLOGIA (filas 2 en adelante)
        $nombresOrdenados = [];
        $maxRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $maxRow; $row++) {
            $id     = trim((string) $sheet->getCell('A' . $row)->getValue());
            $nombre = trim((string) $sheet->getCell('B' . $row)->getValue());
            if ($id !== '' && $nombre !== '') {
                $nombresOrdenados[intval($id)] = $nombre;
            }
        }
        ksort($nombresOrdenados);

        // Crear/encontrar símbolos y construir mapa char → simbolo_id
        $charToId = [];
        foreach ($this->symbolNames as $char => $nombreExpected) {
            // Buscar ID numérico que coincida con el nombre
            $simoloId = null;
            foreach ($nombresOrdenados as $id => $nombre) {
                if (strtoupper(trim($nombre)) === strtoupper($nombreExpected)) {
                    $simoloId = $id;
                    break;
                }
            }

            $simbolo = Simbolo::firstOrCreate(
                ['nombre' => $nombreExpected],
                ['descripcion' => null]
            );
            $charToId[$char] = $simbolo->id;
        }

        return $charToId;
    }

    private function importarProductos($sheet, array $simboloIdMap): void
    {
        $currentFamilia   = null;
        $currentFamiliaId = null;
        $currentCategoriaId = null;
        $familiaOrden     = 0;
        $categoriaOrden   = 0;
        $productoOrden    = 0;

        $creados    = 0;
        $actualizados = 0;
        $skipped    = 0;

        for ($row = 1; $row <= 1034; $row++) {
            $a = trim((string) $sheet->getCell('A' . $row)->getValue());

            // Fila vacía en col A → skip
            if ($a === '') {
                continue;
            }

            // ── FILA DE FAMILIA / CATEGORÍA (A es numérico) ──────────────
            if (is_numeric($a)) {
                $b = trim((string) $sheet->getCell('B' . $row)->getValue());
                $c = trim((string) $sheet->getCell('C' . $row)->getValue());
                $d = trim((string) $sheet->getCell('D' . $row)->getValue());

                // Saltar si C es el header de columnas (Presentación) Y C es genérico
                // (row 1 = header global, row 3 tiene D=Presentación también)
                // Solo procesar si C no es el título de columna global
                if ($c === 'Descripción' || ($b === 'Cod.' && $c === 'Descripción')) {
                    continue;
                }

                // B puede ser nombre de familia o "Cod." / "Cod"
                $bUpper = strtoupper(trim($b));
                if ($bUpper !== 'COD.' && $bUpper !== 'COD') {
                    // Actualizar familia
                    $currentFamilia = $b;
                    $familia = Familia::firstOrCreate(
                        ['titulo' => $b],
                        [
                            'orden'     => ++$familiaOrden,
                            'visible'   => true,
                            'destacado' => false,
                        ]
                    );
                    $currentFamiliaId = $familia->id;
                }

                // Actualizar categoría (siempre, si C no está vacío y no es "Descripción")
                if ($c !== '' && $c !== 'Descripción' && $currentFamiliaId !== null) {
                    $categoria = CategoriaTodotex::firstOrCreate(
                        ['titulo' => $c, 'familia_id' => $currentFamiliaId],
                        [
                            'orden'     => ++$categoriaOrden,
                            'visible'   => true,
                            'destacado' => false,
                        ]
                    );
                    $currentCategoriaId = $categoria->id;
                }

                continue;
            }

            // ── FILA DE PRODUCTO (A es símbolo) ──────────────────────────
            $simboloId = $simboloIdMap[$a] ?? null;
            if ($simboloId === null) {
                // Símbolo desconocido → skip con log
                $skipped++;
                $this->command->warn("Row $row: símbolo desconocido [$a] - skipped");
                continue;
            }

            $codigo       = trim((string) $sheet->getCell('B' . $row)->getValue());
            $descripcion  = trim((string) $sheet->getCell('C' . $row)->getValue());
            $presentacion = trim((string) $sheet->getCell('D' . $row)->getValue());
            $precioPaq    = $sheet->getCell('E' . $row)->getValue();
            $precioUnit   = $sheet->getCell('F' . $row)->getValue();
            $precioKg     = $sheet->getCell('G' . $row)->getValue();
            $pctAumento   = $sheet->getCell('H' . $row)->getValue();

            if ($codigo === '' || $descripcion === '') {
                $skipped++;
                continue;
            }

            $bulto = $this->calcularBulto($presentacion, $precioUnit, $precioPaq);
            $colorData = ProductoTodotex::deriveColorData($codigo);

            $data = [
                'titulo'            => mb_substr($descripcion, 0, 255),
                'descripcion'       => $descripcion,
                'codigo_color'      => $colorData['codigo_color'],
                'nombre_color'      => $colorData['nombre_color'],
                'presentacion'      => $presentacion,
                'precio_paquete'    => $this->toDecimal($precioPaq),
                'precio_unitario'   => $this->toDecimal($precioUnit),
                'precio_kg'         => $this->toDecimal($precioKg),
                'porcentaje_aumento'=> $this->toDecimal($pctAumento !== null ? $pctAumento * 100 : null),
                'bulto'             => $bulto,
                'bulto_cantidad'    => $bulto,
                'simbolo_id'        => $simboloId,
                'visible'           => true,
                'destacado'         => false,
            ];

            $existing = ProductoTodotex::where('codigo', $codigo)->first();

            if ($existing) {
                $existing->update($data);
                $productoActualizado = $existing;
                $actualizados++;
            } else {
                $productoActualizado = ProductoTodotex::create(array_merge($data, [
                    'codigo' => $codigo,
                    'orden'  => ++$productoOrden,
                ]));
                $creados++;
            }

            // Asignar categoría (si tenemos una activa)
            if ($currentCategoriaId !== null) {
                // Verificar si ya está asignada (para evitar duplicados en update)
                if (! $productoActualizado->categorias()->where('categorias_todotex.id', $currentCategoriaId)->exists()) {
                    $productoActualizado->categorias()->attach($currentCategoriaId);
                }
            }
        }

        $this->command->info("Importación completada:");
        $this->command->info("  Creados:    $creados");
        $this->command->info("  Actualizados: $actualizados");
        $this->command->info("  Skipped:    $skipped");
    }

    /**
     * Calcula el bulto basándose en presentación y precios.
     * Si precio_unitario ≈ precio_paquete → bulto = 1 (venta por unidad/peso)
     * Si difieren → extrae el primer número de presentación y verifica multiplicación
     */
    private function calcularBulto(?string $presentacion, $precioUnit, $precioPaq): int
    {
        $pu = floatval($precioUnit);
        $pp = floatval($precioPaq);

        if ($pu <= 0 || $pp <= 0) {
            return 1;
        }

        // Iguales (dentro del 0.1%) → venta por unidad
        if (abs($pu - $pp) / max($pu, 0.001) < 0.001) {
            return 1;
        }

        // Buscar primer número en la presentación
        if (preg_match('/(\d+)/', $presentacion ?? '', $matches)) {
            $n = intval($matches[1]);
            if ($n > 0) {
                // Verificar si unit * n ≈ paquete (tolerancia 1%)
                $esperado = $pu * $n;
                if (abs($esperado - $pp) / max($pp, 0.001) < 0.01) {
                    return $n;
                }
            }
        }

        return 1;
    }

    private function toDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        return round(floatval($value), 2);
    }
}
