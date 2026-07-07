<?php

namespace Database\Seeders;

use App\Models\CategoriaTodotex;
use App\Models\Familia;
use App\Models\ProductoTodotex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ListovichFamiliasCategoriasSeeder extends Seeder
{
    private const SHEET_INDEXES = [7, 8, 9, 10, 11, 12];

    private array $exactProductIdsByCode = [];
    private array $variantProductIdsByBaseCode = [];

    public function run(): void
    {
        $file = database_path('seeders/files/Listovich.xlsx');

        $this->command->info('Cargando familias y categorias desde Listovich.xlsx...');

        $spreadsheet = IOFactory::load($file);
        $this->bootstrapProductLookups();

        $familiasCreated = 0;
        $familiasUpdated = 0;
        $categoriasCreated = 0;
        $categoriasUpdated = 0;
        $missingCodes = [];
        $assignmentsByCategory = [];
        $assignedProductIds = [];

        foreach (self::SHEET_INDEXES as $sheetIndex) {
            $sheet = $spreadsheet->getSheet($sheetIndex);
            $sheetNumber = $sheetIndex + 1;

            [$familia, $familiaCreated, $familiaUpdated] = $this->upsertFamilia(
                $this->normalizeText($sheet->getTitle()),
                $sheetNumber
            );

            $familiasCreated += (int) $familiaCreated;
            $familiasUpdated += (int) $familiaUpdated;

            $categorias = $this->parseSheetCategories($sheet);

            foreach ($categorias as $categoryPosition => $categoryData) {
                [$categoria, $categoriaCreated, $categoriaUpdated] = $this->upsertCategoria(
                    $familia,
                    $categoryData['titulo'],
                    $sheetNumber,
                    $categoryPosition + 1
                );

                $categoriasCreated += (int) $categoriaCreated;
                $categoriasUpdated += (int) $categoriaUpdated;

                foreach ($categoryData['codigos'] as $codigo) {
                    $productIds = $this->resolveProductIds($codigo);

                    if ($productIds === []) {
                        $missingCodes[$sheet->getTitle()][$categoryData['titulo']][] = $codigo;
                        continue;
                    }

                    foreach ($productIds as $productId) {
                        $assignmentsByCategory[$categoria->id][$productId] = true;
                        $assignedProductIds[$productId] = true;
                    }
                }
            }
        }

        $assignedProductIds = array_keys($assignedProductIds);
        $detached = 0;
        $attached = 0;

        DB::transaction(function () use ($assignedProductIds, $assignmentsByCategory, &$detached, &$attached): void {
            if ($assignedProductIds === []) {
                return;
            }

            $pivotIds = DB::table('categoria_producto as cp')
                ->join('categorias_todotex as c', 'c.id', '=', 'cp.categoria_id')
                ->whereIn('cp.producto_id', $assignedProductIds)
                ->whereNull('c.rubro_id')
                ->pluck('cp.id')
                ->all();

            if ($pivotIds !== []) {
                $detached = DB::table('categoria_producto')
                    ->whereIn('id', $pivotIds)
                    ->delete();
            }

            $now = now();
            $rows = [];

            foreach ($assignmentsByCategory as $categoryId => $productIdMap) {
                foreach (array_keys($productIdMap) as $productId) {
                    $rows[] = [
                        'categoria_id' => (int) $categoryId,
                        'producto_id' => (int) $productId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('categoria_producto')->insert($chunk);
                $attached += count($chunk);
            }
        });

        $missingCount = collect($missingCodes)
            ->flatten(2)
            ->count();

        $this->command->info('Importacion de familias/categorias completada:');
        $this->command->info("  Familias creadas: {$familiasCreated}");
        $this->command->info("  Familias actualizadas: {$familiasUpdated}");
        $this->command->info("  Categorias creadas: {$categoriasCreated}");
        $this->command->info("  Categorias actualizadas: {$categoriasUpdated}");
        $this->command->info("  Asociaciones anteriores de familia removidas: {$detached}");
        $this->command->info("  Asociaciones producto-categoria agregadas: {$attached}");
        $this->command->info("  Codigos sin producto encontrado: {$missingCount}");

        foreach ($missingCodes as $familia => $categories) {
            foreach ($categories as $categoria => $codigos) {
                $this->command->warn("  Sin producto en {$familia} / {$categoria}: " . implode(', ', array_unique($codigos)));
            }
        }
    }

    private function bootstrapProductLookups(): void
    {
        $productos = ProductoTodotex::query()->get(['id', 'codigo']);

        foreach ($productos as $producto) {
            $codigo = trim((string) $producto->codigo);

            if ($codigo === '') {
                continue;
            }

            $this->exactProductIdsByCode[$codigo][] = (int) $producto->id;

            if (preg_match('/^(.+)(4\d{3})$/', $codigo, $matches)) {
                $baseCode = $matches[1];
                $this->variantProductIdsByBaseCode[$baseCode][] = (int) $producto->id;
            }
        }
    }

    private function parseSheetCategories($sheet): array
    {
        $categories = [];
        $currentIndex = null;
        $highestRow = $sheet->getHighestRow();

        for ($row = 1; $row <= $highestRow; $row++) {
            $codeCell = $this->normalizeText($sheet->getCell('D' . $row)->getFormattedValue());
            $titleCell = $this->normalizeText($sheet->getCell('E' . $row)->getFormattedValue());

            if ($this->isCategoryHeader($codeCell, $titleCell)) {
                $categories[] = [
                    'titulo' => $titleCell,
                    'codigos' => [],
                ];
                $currentIndex = array_key_last($categories);
                continue;
            }

            if ($currentIndex !== null && preg_match('/^\d+$/', $codeCell)) {
                $categories[$currentIndex]['codigos'][] = $codeCell;
            }
        }

        return array_values(array_filter(array_map(function (array $category) {
            $category['codigos'] = array_values(array_unique($category['codigos']));

            return $category['titulo'] !== '' ? $category : null;
        }, $categories)));
    }

    private function isCategoryHeader(string $codeCell, string $titleCell): bool
    {
        if ($titleCell === '') {
            return false;
        }

        return in_array($this->normalizeKey($codeCell), ['cod', 'cod.'], true);
    }

    private function upsertFamilia(string $titulo, int $sheetNumber): array
    {
        $familia = $this->findFamiliaByTitle($titulo) ?? new Familia(['titulo' => $titulo]);
        $wasCreated = ! $familia->exists;

        if ($wasCreated) {
            $familia->orden = sprintf('LISTOVICH-%02d', $sheetNumber);
            $familia->destacado = false;
        }

        $familia->visible = true;

        $wasDirty = $familia->isDirty();
        if ($wasCreated || $wasDirty) {
            $familia->save();
        }

        return [$familia, $wasCreated, ! $wasCreated && $wasDirty];
    }

    private function upsertCategoria(Familia $familia, string $titulo, int $sheetNumber, int $categoryPosition): array
    {
        $categoria = $this->findCategoriaByTitle($familia, $titulo) ?? new CategoriaTodotex([
            'titulo' => $titulo,
            'familia_id' => $familia->id,
            'rubro_id' => null,
        ]);

        $wasCreated = ! $categoria->exists;

        $categoria->orden = sprintf('LISTOVICH-%02d-%03d', $sheetNumber, $categoryPosition);
        $categoria->visible = true;

        if ($wasCreated) {
            $categoria->destacado = false;
        }

        $wasDirty = $categoria->isDirty();
        if ($wasCreated || $wasDirty) {
            $categoria->save();
        }

        return [$categoria, $wasCreated, ! $wasCreated && $wasDirty];
    }

    private function findFamiliaByTitle(string $titulo): ?Familia
    {
        $targetKey = $this->normalizeKey($titulo);

        return Familia::query()
            ->get()
            ->first(fn (Familia $familia) => $this->normalizeKey($familia->titulo) === $targetKey);
    }

    private function findCategoriaByTitle(Familia $familia, string $titulo): ?CategoriaTodotex
    {
        $targetKey = $this->normalizeKey($titulo);

        return CategoriaTodotex::query()
            ->where('familia_id', $familia->id)
            ->whereNull('rubro_id')
            ->get()
            ->first(fn (CategoriaTodotex $categoria) => $this->normalizeKey($categoria->titulo) === $targetKey);
    }

    private function resolveProductIds(string $codigo): array
    {
        $productIds = array_merge(
            $this->exactProductIdsByCode[$codigo] ?? [],
            $this->variantProductIdsByBaseCode[$codigo] ?? []
        );

        return array_values(array_unique(array_map('intval', $productIds)));
    }

    private function normalizeText($value): string
    {
        $value = trim((string) $value);

        return preg_replace('/\s+/u', ' ', $value) ?? '';
    }

    private function normalizeKey(string $value): string
    {
        return Str::of($this->normalizeText($value))
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9&]+/', ' ')
            ->squish()
            ->toString();
    }
}
