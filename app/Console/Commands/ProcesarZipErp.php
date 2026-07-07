<?php

namespace App\Console\Commands;

use App\Models\BulkImportLog;
use App\Services\Imports\Erp\ErpZipImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Throwable;
use ZipArchive;

class ProcesarZipErp extends Command
{
    protected $signature = 'imports:procesar-zip-erp
                            {--zip= : Ruta absoluta del ZIP a procesar (si no se pasa, busca en imports-erp/in/)}
                            {--force : Procesar aunque ya exista uno con el mismo nombre en processed/}';

    protected $description = 'Procesa el .zip con los Excel del ERP, importa todas las tablas en una sola transacción y mueve el archivo a processed/.';

    public function handle(): int
    {
        // Por las dudas: aunque CLI no suele tener timeout, lo deshabilitamos.
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $zipPath = $this->option('zip') ?: $this->localizarZipMasReciente();

        if (!$zipPath) {
            $this->info('No hay ZIP nuevo para procesar.');
            return self::SUCCESS;
        }

        if (!is_file($zipPath)) {
            $this->error("Archivo no encontrado: {$zipPath}");
            return self::FAILURE;
        }

        $log = BulkImportLog::create([
            'proceso' => 'erp_zip',
            'archivo' => basename($zipPath),
            'estado'  => BulkImportLog::ESTADO_OK,
        ]);

        $this->info("Procesando: " . basename($zipPath));

        try {
            $extractDir = $this->extraerZip($zipPath);

            $importer = new ErpZipImporter();
            $procesadas = $importer->importar($extractDir);

            $resumen = collect($procesadas)
                ->map(fn ($n, $tabla) => "{$tabla}: {$n}")
                ->implode(', ');

            $this->info("✔ Import OK — {$resumen}");

            $rutaProcesado = $this->moverAProcesados($zipPath);
            $this->line("→ Movido a: {$rutaProcesado}");

            $log->update([
                'estado'           => BulkImportLog::ESTADO_OK,
                'filas_procesadas' => array_sum($procesadas),
                'mensaje'          => "Import OK. {$resumen}. Archivo movido a " . basename($rutaProcesado),
                'detalle_errores'  => null,
            ]);

            File::deleteDirectory($extractDir);
            return self::SUCCESS;

        } catch (Throwable $e) {
            // Truncamos a ~4KB para evitar overflow de columnas y mantener legibilidad
            $mensajeCorto = mb_substr($e->getMessage(), 0, 3500);
            $log->update([
                'estado'  => BulkImportLog::ESTADO_ERROR,
                'mensaje' => 'Error: ' . $mensajeCorto . ' (rollback aplicado — datos originales intactos)',
                'detalle_errores' => [
                    'message' => $mensajeCorto,
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ],
            ]);
            $this->error('Error: ' . $mensajeCorto);
            $this->error('Rollback aplicado. Datos originales intactos.');
            return self::FAILURE;
        }
    }

    private function localizarZipMasReciente(): ?string
    {
        $inDir = $this->dirIn();
        if (!is_dir($inDir)) {
            return null;
        }
        $zips = glob($inDir . DIRECTORY_SEPARATOR . '*.zip') ?: [];
        if (empty($zips)) {
            return null;
        }
        usort($zips, fn ($a, $b) => filemtime($b) <=> filemtime($a));
        return $zips[0];
    }

    private function extraerZip(string $zipPath): string
    {
        $extractDir = storage_path('app/erp/extract-' . uniqid('', true));
        File::ensureDirectoryExists($extractDir);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException("No se pudo abrir el ZIP: {$zipPath}");
        }
        $zip->extractTo($extractDir);
        $zip->close();
        return $extractDir;
    }

    private function moverAProcesados(string $zipPath): string
    {
        $processedDir = $this->dirProcessed();
        File::ensureDirectoryExists($processedDir);

        $base = basename($zipPath, '.zip');
        $destino = $processedDir . DIRECTORY_SEPARATOR
            . Carbon::now()->format('Ymd-His') . '_' . $base . '.zip';

        if (!@rename($zipPath, $destino)) {
            // Fallback (en caso de que rename falle entre filesystems)
            File::copy($zipPath, $destino);
            File::delete($zipPath);
        }
        return $destino;
    }

    private function dirIn(): string
    {
        return rtrim(config('services.erp.ftp_in_path') ?: base_path('erp/in'), '/\\');
    }

    private function dirProcessed(): string
    {
        return rtrim(config('services.erp.ftp_processed_path') ?: base_path('erp/processed'), '/\\');
    }
}
