<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Pone en NULL el campo `orden` de todas las categorías Todotex.
 * Útil para que el cliente empiece a asignar orden manual desde cero.
 *
 * Las categorías sin orden irán al final del sidebar de filtros.
 * Las que tengan orden (1, 2, AA, AB…) aparecerán primero.
 *
 * Uso:
 *   php artisan db:seed --class=NullOrdenCategoriasTodotexSeeder
 *
 * Dry-run (solo muestra el conteo, sin modificar):
 *   DRY_RUN=true php artisan db:seed --class=NullOrdenCategoriasTodotexSeeder
 */
class NullOrdenCategoriasTodotexSeeder extends Seeder
{
    public function run(): void
    {
        $isDryRun = filter_var(env('DRY_RUN', false), FILTER_VALIDATE_BOOLEAN);

        $total    = DB::table('categorias_todotex')->count();
        $conOrden = DB::table('categorias_todotex')->whereNotNull('orden')->count();
        $sinOrden = $total - $conOrden;

        $this->command?->info("Categorías totales:    {$total}");
        $this->command?->info("Con orden asignado:    {$conOrden}");
        $this->command?->info("Ya en NULL:            {$sinOrden}");

        if ($conOrden === 0) {
            $this->command?->info('Todas las categorías ya tienen orden = NULL. Nada que hacer.');
            return;
        }

        if ($isDryRun) {
            $this->command?->warn("DRY_RUN=true — Se pondrían en NULL {$conOrden} categorías. Quitá DRY_RUN para ejecutar.");
            return;
        }

        DB::table('categorias_todotex')->update(['orden' => null]);

        $this->command?->info("✓ {$conOrden} categorías actualizadas a orden = NULL.");
    }
}
