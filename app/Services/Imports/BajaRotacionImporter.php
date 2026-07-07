<?php

namespace App\Services\Imports;

use App\Models\CategoriaTodotex;
use App\Models\ColorTodotex;
use App\Models\Familia;
use App\Models\Rubro;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class BajaRotacionImporter
{
    /**
     * Reemplaza Rubros, Familias, Categorías y Colores desde un Excel multi-hoja.
     *
     * El archivo debe tener UNA hoja por tabla, con estos nombres (case-insensitive):
     *   - "rubros"            columnas: titulo, orden
     *   - "familias"          columnas: titulo, orden, destacado, visible
     *   - "categorias"        columnas: titulo, orden, destacado, visible, familia, rubro
     *   - "colores"           columnas: titulo, codigo_color, orden
     *
     * Si una hoja falta se la ignora; si una columna obligatoria falta se aborta y
     * BulkImportRunner hace rollback.
     */
    public function fromFile(UploadedFile $archivo): int
    {
        $hojas = Excel::toArray([], $archivo);

        $rubrosRows     = $this->sheetAt($hojas, 0);
        $familiasRows   = $this->sheetAt($hojas, 1);
        $categoriasRows = $this->sheetAt($hojas, 2);
        $coloresRows    = $this->sheetAt($hojas, 3);

        if (!$rubrosRows && !$familiasRows && !$categoriasRows && !$coloresRows) {
            throw new RuntimeException("El archivo no contiene hojas reconocibles (rubros, familias, categorias, colores).");
        }

        // Borrar e insertar en orden de dependencias inversas
        CategoriaTodotex::query()->delete();
        Familia::query()->delete();
        Rubro::query()->delete();
        ColorTodotex::query()->delete();

        $total = 0;

        $mapaRubros = [];
        foreach ($rubrosRows as $row) {
            $titulo = $this->str($row, 'titulo');
            if (!$titulo) continue;
            $rubro = Rubro::create([
                'titulo' => $titulo,
                'orden'  => (int) ($this->num($row, 'orden') ?? 0),
            ]);
            $mapaRubros[mb_strtolower($titulo)] = $rubro->id;
            $total++;
        }

        $mapaFamilias = [];
        foreach ($familiasRows as $row) {
            $titulo = $this->str($row, 'titulo');
            if (!$titulo) continue;
            $familia = Familia::create([
                'titulo'    => $titulo,
                'orden'     => (int) ($this->num($row, 'orden') ?? 0),
                'destacado' => $this->bool($row, 'destacado'),
                'visible'   => $this->boolOrTrue($row, 'visible'),
            ]);
            $mapaFamilias[mb_strtolower($titulo)] = $familia->id;
            $total++;
        }

        foreach ($categoriasRows as $row) {
            $titulo = $this->str($row, 'titulo');
            if (!$titulo) continue;
            $familiaKey = mb_strtolower((string) $this->str($row, 'familia'));
            $rubroKey   = mb_strtolower((string) $this->str($row, 'rubro'));
            CategoriaTodotex::create([
                'titulo'     => $titulo,
                'orden'      => (int) ($this->num($row, 'orden') ?? 0),
                'destacado'  => $this->bool($row, 'destacado'),
                'visible'    => $this->boolOrTrue($row, 'visible'),
                'familia_id' => $mapaFamilias[$familiaKey] ?? null,
                'rubro_id'   => $mapaRubros[$rubroKey] ?? null,
            ]);
            $total++;
        }

        foreach ($coloresRows as $row) {
            $titulo = $this->str($row, 'titulo');
            if (!$titulo) continue;
            ColorTodotex::create([
                'titulo'       => $titulo,
                'codigo_color' => $this->str($row, 'codigo_color'),
                'orden'        => (int) ($this->num($row, 'orden') ?? 0),
            ]);
            $total++;
        }

        return $total;
    }

    /**
     * Devuelve las filas (assoc por cabecera) de la hoja $idx (0-indexed).
     * Si la hoja no existe o está vacía, devuelve [].
     */
    private function sheetAt(array $hojas, int $idx): array
    {
        if (!isset($hojas[$idx]) || empty($hojas[$idx])) {
            return [];
        }
        $sheet   = $hojas[$idx];
        $headers = array_map(fn ($h) => $this->normalize((string) $h), array_shift($sheet));
        $rows    = [];
        foreach ($sheet as $r) {
            if ($this->isEmpty($r)) {
                continue;
            }
            $assoc = [];
            foreach ($headers as $i => $h) {
                $assoc[$h] = $r[$i] ?? null;
            }
            $rows[] = $assoc;
        }
        return $rows;
    }

    private function normalize(string $h): string
    {
        $h = mb_strtolower(trim($h));
        $h = strtr($h, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n', ' ' => '_', '-' => '_']);
        return preg_replace('/[^a-z0-9_]/', '', $h);
    }

    private function isEmpty(array $row): bool
    {
        foreach ($row as $c) {
            if ($c !== null && $c !== '') return false;
        }
        return true;
    }

    private function str(array $row, string $key): ?string
    {
        $v = $row[$key] ?? null;
        if ($v === null) return null;
        $v = trim((string) $v);
        return $v === '' ? null : $v;
    }

    private function num(array $row, string $key): ?float
    {
        $v = $this->str($row, $key);
        return $v !== null && is_numeric(str_replace(',', '.', $v)) ? (float) str_replace(',', '.', $v) : null;
    }

    private function bool(array $row, string $key): bool
    {
        $v = $this->str($row, $key);
        return in_array(mb_strtoupper((string) $v), ['S', 'SI', '1', 'TRUE', 'Y', 'YES'], true);
    }

    private function boolOrTrue(array $row, string $key): bool
    {
        $v = $this->str($row, $key);
        if ($v === null) return true;
        return in_array(mb_strtoupper($v), ['S', 'SI', '1', 'TRUE', 'Y', 'YES'], true);
    }
}
