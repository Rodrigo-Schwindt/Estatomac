<?php

namespace App\Services\Imports;

use App\Models\BulkImportLog;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class BulkImportRunner
{
    /**
     * Ejecuta un import dentro de una transacción.
     * Si la callback tira excepción, la BD vuelve al estado original y se registra el error.
     *
     * @param  string          $proceso  Identificador del proceso (ej: "clientes_canales").
     * @param  UploadedFile    $archivo  Archivo subido.
     * @param  Closure         $callback function(array $rows): int  -> devuelve filas procesadas.
     * @return BulkImportLog
     */
    public function run(string $proceso, UploadedFile $archivo, Closure $callback): BulkImportLog
    {
        $log = BulkImportLog::create([
            'proceso' => $proceso,
            'archivo' => $archivo->getClientOriginalName(),
            'estado'  => BulkImportLog::ESTADO_OK,
            'user_id' => Auth::id(),
        ]);

        try {
            $rows = $this->readRows($archivo);

            if (empty($rows)) {
                $log->update([
                    'estado'  => BulkImportLog::ESTADO_VACIO,
                    'mensaje' => 'El archivo no contiene filas con datos.',
                ]);
                return $log;
            }

            $procesadas = DB::transaction(function () use ($callback, $rows) {
                return (int) $callback($rows);
            });

            $log->update([
                'estado'           => BulkImportLog::ESTADO_OK,
                'filas_procesadas' => $procesadas,
                'mensaje'          => "Carga completada: {$procesadas} fila(s).",
            ]);

            return $log;
        } catch (Throwable $e) {
            Log::error("BulkImport [{$proceso}] error", ['exception' => $e]);
            $log->update([
                'estado'  => BulkImportLog::ESTADO_ERROR,
                'mensaje' => 'Error: ' . $e->getMessage() . ' (rollback aplicado, los datos originales quedan operativos)',
            ]);
            return $log;
        }
    }

    /**
     * Lee filas desde Excel/CSV y devuelve array assoc usando la primera fila como cabecera.
     */
    private function readRows(UploadedFile $archivo): array
    {
        $raw = Excel::toArray([], $archivo);
        if (empty($raw) || empty($raw[0])) {
            return [];
        }

        $sheet = $raw[0];
        $headers = array_map(fn ($h) => $this->normalizeHeader((string) $h), array_shift($sheet));

        $rows = [];
        foreach ($sheet as $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }
            $assoc = [];
            foreach ($headers as $i => $h) {
                $assoc[$h] = $row[$i] ?? null;
            }
            $rows[] = $assoc;
        }
        return $rows;
    }

    private function normalizeHeader(string $h): string
    {
        $h = mb_strtolower(trim($h));
        $h = strtr($h, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
            ' ' => '_', '-' => '_', '.' => '_', '/' => '_',
        ]);
        return preg_replace('/[^a-z0-9_]/', '', $h);
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && $cell !== '') return false;
        }
        return true;
    }
}
