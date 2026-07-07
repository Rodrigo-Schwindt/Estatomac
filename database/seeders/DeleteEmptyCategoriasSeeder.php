<?php

namespace Database\Seeders;

use App\Models\CategoriaTodotex;
use Illuminate\Database\Seeder;

/**
 * Elimina las categorías de Todotex que no tienen ningún producto asignado.
 *
 * Uso:
 *   php artisan db:seed --class=DeleteEmptyCategoriasSeeder
 *
 * Dry-run (solo muestra qué se eliminaría, sin tocar la BD):
 *   DRY_RUN=true php artisan db:seed --class=DeleteEmptyCategoriasSeeder
 */
class DeleteEmptyCategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $isDryRun = filter_var(env('DRY_RUN', false), FILTER_VALIDATE_BOOLEAN);

        $categorias = CategoriaTodotex::doesntHave('productos')
            ->with('familia', 'rubro')
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'orden', 'familia_id', 'rubro_id']);

        if ($categorias->isEmpty()) {
            $this->command?->info('No hay categorías sin productos. Nada que eliminar.');
            return;
        }

        $this->command?->info("Categorías sin productos ({$categorias->count()}):");

        foreach ($categorias as $cat) {
            $familia = $cat->familia?->titulo ?? '—';
            $rubro   = $cat->rubro?->titulo   ?? '—';
            $this->command?->line("  ID: {$cat->id} | {$cat->titulo} | Familia: {$familia} | Rubro: {$rubro}");
        }

        if ($isDryRun) {
            $this->command?->warn('DRY_RUN=true — No se eliminó nada. Quitá DRY_RUN para ejecutar de verdad.');
            return;
        }

        $ids = $categorias->pluck('id')->all();

        CategoriaTodotex::whereIn('id', $ids)->delete();

        $this->command?->info('✓ ' . count($ids) . ' categorías eliminadas correctamente.');
    }
}
