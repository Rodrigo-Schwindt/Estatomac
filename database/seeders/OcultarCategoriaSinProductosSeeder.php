<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Oculta (visible = false) todas las categorías Todotex que no tienen
 * ningún producto asignado en la tabla pivot categoria_producto.
 *
 * Uso:
 *   php artisan db:seed --class=OcultarCategoriaSinProductosSeeder
 *
 * Dry-run (muestra qué se ocultaría sin modificar):
 *   DRY_RUN=true php artisan db:seed --class=OcultarCategoriaSinProductosSeeder
 */
class OcultarCategoriaSinProductosSeeder extends Seeder
{
    public function run(): void
    {
        $isDryRun = filter_var(env('DRY_RUN', false), FILTER_VALIDATE_BOOLEAN);

        $total     = DB::table('categorias_todotex')->count();
        $yaOcultas = DB::table('categorias_todotex')->where('visible', false)->count();

        // IDs de categorías que SÍ tienen al menos un producto
        $conProductos = DB::table('categoria_producto')
            ->distinct()
            ->pluck('categoria_id');

        // Categorías visibles sin productos
        $sinProductos = DB::table('categorias_todotex')
            ->where('visible', true)
            ->whereNotIn('id', $conProductos)
            ->get(['id', 'titulo', 'familia_id']);

        $this->command?->info("Categorías totales:              {$total}");
        $this->command?->info("Ya ocultas:                      {$yaOcultas}");
        $this->command?->info("Visibles sin productos asignados: {$sinProductos->count()}");

        if ($sinProductos->isEmpty()) {
            $this->command?->info('No hay categorías visibles sin productos. Nada que hacer.');
            return;
        }

        if ($isDryRun) {
            $this->command?->warn("DRY_RUN=true — Se ocultarían {$sinProductos->count()} categorías:");
            foreach ($sinProductos as $cat) {
                $this->command?->line("  [{$cat->id}] {$cat->titulo}");
            }
            $this->command?->warn("Quitá DRY_RUN para ejecutar.");
            return;
        }

        $ids = $sinProductos->pluck('id');
        DB::table('categorias_todotex')->whereIn('id', $ids)->update(['visible' => false]);

        $this->command?->info("✓ {$sinProductos->count()} categorías ocultadas:");
        foreach ($sinProductos as $cat) {
            $this->command?->line("  [{$cat->id}] {$cat->titulo}");
        }
    }
}
