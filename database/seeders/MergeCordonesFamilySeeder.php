<?php

namespace Database\Seeders;

use App\Models\CategoriaTodotex;
use App\Models\Familia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MergeCordonesFamilySeeder extends Seeder
{
    public function run(): void
    {
        $cordones = $this->findFamilia('Cordones');

        if (! $cordones) {
            $this->command->info('La familia Cordones ya no existe.');

            return;
        }

        $cordoneria = $this->findFamilia('Cordonería');

        if (! $cordoneria) {
            throw new \RuntimeException('No se encontro la familia Cordoneria.');
        }

        $categories = CategoriaTodotex::query()
            ->where('familia_id', $cordones->id)
            ->withCount('productos')
            ->orderBy('id')
            ->get();

        $movedCategories = [];
        $deletedCategories = [];

        DB::transaction(function () use ($categories, $cordones, $cordoneria, &$movedCategories, &$deletedCategories): void {
            foreach ($categories as $category) {
                if ($this->categoryProtectsCordonesOnlyProducts($category, $cordones)) {
                    $category->familia_id = $cordoneria->id;
                    $category->save();

                    $movedCategories[] = [
                        'id' => $category->id,
                        'titulo' => $category->titulo,
                        'productos' => $category->productos_count,
                    ];

                    continue;
                }

                $deletedCategories[] = [
                    'id' => $category->id,
                    'titulo' => $category->titulo,
                    'productos' => $category->productos_count,
                ];

                $category->delete();
            }

            $cordones->delete();
        });

        $orphanProducts = $this->countProductsWithoutAnyFamily();
        $cordonesStillExists = Familia::query()->whereKey($cordones->id)->exists();

        $this->command->info('Fusion de Cordones completada:');
        $this->command->info('  Categorias movidas a Cordoneria: ' . count($movedCategories));
        $this->command->info('  Categorias eliminadas: ' . count($deletedCategories));
        $this->command->info('  Familia Cordones existe: ' . ($cordonesStillExists ? 'si' : 'no'));
        $this->command->info("  Productos sin familia/categoria: {$orphanProducts}");

        if ($orphanProducts > 0) {
            throw new \RuntimeException("La fusion dejo {$orphanProducts} productos sin familia.");
        }
    }

    private function categoryProtectsCordonesOnlyProducts(CategoriaTodotex $category, Familia $cordones): bool
    {
        return DB::table('categoria_producto as cp_current')
            ->where('cp_current.categoria_id', $category->id)
            ->whereNotExists(function ($query) use ($cordones) {
                $query->select(DB::raw(1))
                    ->from('categoria_producto as cp_other')
                    ->join('categorias_todotex as c_other', 'c_other.id', '=', 'cp_other.categoria_id')
                    ->whereColumn('cp_other.producto_id', 'cp_current.producto_id')
                    ->where('c_other.familia_id', '!=', $cordones->id);
            })
            ->exists();
    }

    private function countProductsWithoutAnyFamily(): int
    {
        return DB::table('productos_todotex as p')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('categoria_producto as cp')
                    ->join('categorias_todotex as c', 'c.id', '=', 'cp.categoria_id')
                    ->whereColumn('cp.producto_id', 'p.id');
            })
            ->count();
    }

    private function findFamilia(string $titulo): ?Familia
    {
        $targetKey = $this->normalizeKey($titulo);

        return Familia::query()
            ->get()
            ->first(fn (Familia $familia) => $this->normalizeKey($familia->titulo) === $targetKey);
    }

    private function normalizeKey(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9&]+/', ' ')
            ->squish()
            ->toString();
    }
}
