<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Pone en NULL el campo `orden` de todos los productos Todotex.
 * Útil para que el cliente empiece a asignar orden manual desde cero.
 *
 * Los productos sin orden quedarán al final del listado público/zona.
 * Los que tengan orden (1, 2, AA, AB…) aparecerán primero.
 *
 * Uso:
 *   php artisan db:seed --class=NullOrdenProductosTodotexSeeder
 *
 * Dry-run (solo muestra el conteo, sin modificar):
 *   DRY_RUN=true php artisan db:seed --class=NullOrdenProductosTodotexSeeder
 */
class NullOrdenProductosTodotexSeeder extends Seeder
{
    public function run(): void
    {
        $isDryRun = filter_var(env('DRY_RUN', false), FILTER_VALIDATE_BOOLEAN);

        $total     = DB::table('productos_todotex')->count();
        $conOrden  = DB::table('productos_todotex')->whereNotNull('orden')->count();
        $sinOrden  = $total - $conOrden;

        $this->command?->info("Productos totales:     {$total}");
        $this->command?->info("Con orden asignado:    {$conOrden}");
        $this->command?->info("Ya en NULL:            {$sinOrden}");

        if ($conOrden === 0) {
            $this->command?->info('Todos los productos ya tienen orden = NULL. Nada que hacer.');
            return;
        }

        if ($isDryRun) {
            $this->command?->warn("DRY_RUN=true — Se pondrían en NULL {$conOrden} productos. Quitá DRY_RUN para ejecutar.");
            return;
        }

        DB::table('productos_todotex')->update(['orden' => null]);

        $this->command?->info("✓ {$conOrden} productos actualizados a orden = NULL.");
    }
}
