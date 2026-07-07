<?php

namespace Database\Seeders;

use App\Models\ProductoTodotex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Elimina los productos que ya no aparecen en la Planilla Actualizada.xlsx
 * (comparación por columna O del archivo).
 *
 * Uso:
 *   php artisan db:seed --class=DeleteObsoleteProductsSeeder
 *
 * Dry-run (solo muestra qué se eliminaría, sin tocar la BD):
 *   DRY_RUN=true php artisan db:seed --class=DeleteObsoleteProductsSeeder
 */
class DeleteObsoleteProductsSeeder extends Seeder
{
    /**
     * Códigos de productos a eliminar (no presentes en Planilla Actualizada.xlsx).
     * Derivados el 2026-05-26 comparando BD vs columna O del Excel.
     */
    private const CODES_TO_DELETE = [
        '344', '346', '345',
        '412', '414', '417',
        '258', '259', '268', '269', '263', '278', '280', '262',
        '1600',
        '796', '797', '794', '795',
        '1366', '1345', '1348',
        '1408', '1409',
        '650', '651', '652',
        '2', '3', '7', '8', '21', '29', '31', '36',
        '1030', '1031', '1029', '1038', '1037', '1040', '1041', '1042', '1043',
        '1129', '1131', '1133', '1138', '1149', '1157', '1189',
        '702', '704', '705', '706', '708',
    ];

    public function run(): void
    {
        $isDryRun = filter_var(env('DRY_RUN', false), FILTER_VALIDATE_BOOLEAN);

        $productos = ProductoTodotex::query()
            ->whereIn(DB::raw('TRIM(codigo)'), self::CODES_TO_DELETE)
            ->orderBy('codigo')
            ->get(['id', 'codigo', 'titulo']);

        if ($productos->isEmpty()) {
            $this->command?->info('No se encontraron productos con esos códigos. Nada que eliminar.');
            return;
        }

        $this->command?->info('Productos a eliminar (' . $productos->count() . '):');
        foreach ($productos as $producto) {
            $this->command?->line("  ID: {$producto->id} | Código: {$producto->codigo} | {$producto->titulo}");
        }

        if ($isDryRun) {
            $this->command?->warn('DRY_RUN=true — No se eliminó nada. Quitá DRY_RUN para ejecutar de verdad.');
            return;
        }

        $ids = $productos->pluck('id')->all();

        DB::transaction(function () use ($ids): void {
            // Las tablas relacionadas tienen onDelete('cascade'):
            // - producto_gallery
            // - categoria_producto
            // - carrito (producto_todotex_id)
            ProductoTodotex::query()->whereIn('id', $ids)->delete();
        });

        $this->command?->info('✓ ' . count($ids) . ' productos eliminados correctamente.');
        $this->command?->info('  (galería, categorías y carritos asociados eliminados en cascada)');
    }
}
