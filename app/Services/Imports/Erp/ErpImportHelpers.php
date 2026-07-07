<?php

namespace App\Services\Imports\Erp;

use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;

/**
 * Utilidades comunes para todos los importers del ZIP del ERP.
 */
trait ErpImportHelpers
{
    /**
     * Lee un Excel y devuelve [headers, rowsAsAssoc].
     * Los encabezados quedan tal cual vienen — los importers usan los nombres reales del ERP.
     */
    protected function readExcel(string $path): array
    {
        if (!is_file($path)) {
            throw new RuntimeException("Archivo no encontrado: {$path}");
        }
        $raw = Excel::toArray([], $path);
        if (empty($raw) || empty($raw[0])) {
            return [[], []];
        }
        $sheet = $raw[0];
        $headers = array_map(fn ($h) => (string) $h, array_shift($sheet));

        $rows = [];
        foreach ($sheet as $row) {
            if ($this->isEmpty($row)) continue;
            $assoc = [];
            foreach ($headers as $i => $h) {
                $assoc[$h] = $row[$i] ?? null;
            }
            $rows[] = $assoc;
        }
        return [$headers, $rows];
    }

    protected function isEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && $cell !== '') return false;
        }
        return true;
    }

    protected function str(?string $key, array $row, ?int $maxLen = null): ?string
    {
        if ($key === null) return null;
        $v = $row[$key] ?? null;
        if ($v === null) return null;
        $v = trim((string) $v);
        if ($v === '') return null;
        return $maxLen !== null ? mb_substr($v, 0, $maxLen) : $v;
    }

    protected function int(?string $key, array $row): ?int
    {
        $v = $this->str($key, $row);
        if ($v === null) return null;
        return is_numeric($v) ? (int) $v : null;
    }

    protected function num(?string $key, array $row): ?float
    {
        $v = $this->str($key, $row);
        if ($v === null) return null;
        $v = str_replace(',', '.', $v);
        return is_numeric($v) ? (float) $v : null;
    }

    protected function sn(?string $key, array $row): bool
    {
        $v = mb_strtoupper((string) $this->str($key, $row));
        return $v === 'S';
    }

    /** Devuelve el char 'S' o 'N' tal cual viene (para guardar como flag textual). */
    protected function snChar(?string $key, array $row): ?string
    {
        $v = $this->str($key, $row);
        return $v !== null ? mb_strtoupper(mb_substr($v, 0, 1)) : null;
    }

    /** Parsea fechas tipo "30/01/2025 00:00:00" o "2025-01-30" → 'YYYY-MM-DD'. */
    protected function date(?string $key, array $row): ?string
    {
        $v = $this->str($key, $row);
        if ($v === null) return null;

        // Excel a veces devuelve serial date (float)
        if (is_numeric($v)) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $v);
                return $dt->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        // dd/mm/yyyy [HH:MM:SS]
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})/', $v, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        // yyyy-mm-dd
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $v, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        try {
            return (new \DateTime($v))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
