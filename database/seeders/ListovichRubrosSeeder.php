<?php

namespace Database\Seeders;

use App\Models\CategoriaTodotex;
use App\Models\Familia;
use App\Models\ProductoTodotex;
use App\Models\Rubro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ListovichRubrosSeeder extends Seeder
{
    private const SHEET_INDEXES = [1, 2, 3, 4, 5, 6];

    private const COLOR_SUFFIXES = [
        '4001', '4002', '4003', '4004', '4005', '4006', '4007',
        '4012', '4013', '4014', '4015', '4017', '4018',
        '4020', '4021', '4022', '4023', '4025', '4026', '4027', '4028', '4029',
        '4030', '4032', '4033', '4034', '4035', '4036', '4038', '4039',
        '4040', '4041', '4042', '4044',
        '4052', '4055', '4056', '4057', '4059',
        '4061', '4062', '4063', '4064',
        '4079',
        '4080', '4081', '4084',
        '4116', '4117', '4118',
        '4120', '4121', '4122', '4123', '4124', '4125',
    ];

    private array $familyIdsBySlug = [];
    private array $familyVotesByCode = [];
    private array $exactProductIdsByCode = [];
    private array $variantProductIdsByBaseCode = [];

    public function run(): void
    {
        $file = database_path('seeders/files/Listovich.xlsx');

        $this->command->info('Cargando Listovich.xlsx...');

        $spreadsheet = IOFactory::load($file);

        $this->bootstrapLookups();

        $rubrosCreated = 0;
        $rubrosUpdated = 0;
        $categoriasCreated = 0;
        $categoriasUpdated = 0;
        $productosAsociados = 0;
        $categoriasSinProductos = 0;

        foreach (self::SHEET_INDEXES as $sheetPosition => $sheetIndex) {
            $sheet = $spreadsheet->getSheet($sheetIndex);

            [$rubro, $rubroCreated, $rubroUpdated] = $this->upsertRubro(
                $this->normalizeText($sheet->getTitle()),
                $sheetPosition + 1
            );

            $rubrosCreated += (int) $rubroCreated;
            $rubrosUpdated += (int) $rubroUpdated;

            $categorias = $this->parseSheetCategories($sheet);

            foreach ($categorias as $categoryPosition => $categoryData) {
                $familiaId = $this->inferFamiliaId($categoryData['titulo'], $categoryData['codigos']);

                [$categoria, $categoriaCreated, $categoriaUpdated] = $this->upsertCategoria(
                    $rubro,
                    $categoryData['titulo'],
                    $familiaId,
                    $sheetPosition + 1,
                    $categoryPosition + 1
                );

                $categoriasCreated += (int) $categoriaCreated;
                $categoriasUpdated += (int) $categoriaUpdated;

                $attached = $this->attachProductsToCategoria($categoria, $categoryData['codigos']);
                $productosAsociados += $attached;

                if ($attached === 0) {
                    $categoriasSinProductos++;
                }
            }
        }

        $this->command->info('Importacion de rubros completada:');
        $this->command->info("  Rubros creados: {$rubrosCreated}");
        $this->command->info("  Rubros actualizados: {$rubrosUpdated}");
        $this->command->info("  Categorias creadas: {$categoriasCreated}");
        $this->command->info("  Categorias actualizadas: {$categoriasUpdated}");
        $this->command->info("  Nuevas asociaciones producto-categoria: {$productosAsociados}");
        $this->command->info("  Categorias sin productos asociados: {$categoriasSinProductos}");
    }

    private function bootstrapLookups(): void
    {
        $familias = Familia::query()->get(['id', 'titulo']);
        foreach ($familias as $familia) {
            $this->familyIdsBySlug[$this->normalizeKey($familia->titulo)] = (int) $familia->id;
        }

        $votes = DB::table('productos_todotex as p')
            ->join('categoria_producto as cp', 'cp.producto_id', '=', 'p.id')
            ->join('categorias_todotex as c', 'c.id', '=', 'cp.categoria_id')
            ->whereNull('c.rubro_id')
            ->select('p.codigo', 'c.familia_id', DB::raw('COUNT(*) as total'))
            ->groupBy('p.codigo', 'c.familia_id')
            ->get();

        foreach ($votes as $vote) {
            $codigo = trim((string) $vote->codigo);
            $familiaId = (int) $vote->familia_id;
            $this->familyVotesByCode[$codigo][$familiaId] = (int) $vote->total;
        }

        $productos = ProductoTodotex::query()->get(['id', 'codigo']);
        foreach ($productos as $producto) {
            $codigo = trim((string) $producto->codigo);

            if ($codigo === '') {
                continue;
            }

            $this->exactProductIdsByCode[$codigo][] = (int) $producto->id;

            foreach (self::COLOR_SUFFIXES as $suffix) {
                if (strlen($codigo) <= strlen($suffix) || !str_ends_with($codigo, $suffix)) {
                    continue;
                }

                $baseCode = substr($codigo, 0, -strlen($suffix));

                if ($baseCode !== '') {
                    $this->variantProductIdsByBaseCode[$baseCode][] = (int) $producto->id;
                }

                break;
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
            $priceCell = $this->normalizeText($sheet->getCell('G' . $row)->getFormattedValue());
            $bulkCell = $this->normalizeText($sheet->getCell('O' . $row)->getFormattedValue());

            if ($this->isCategoryHeader($codeCell, $titleCell, $priceCell, $bulkCell)) {
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

    private function isCategoryHeader(string $codeCell, string $titleCell, string $priceCell, string $bulkCell): bool
    {
        if ($titleCell === '') {
            return false;
        }

        $normalizedCode = $this->normalizeKey($codeCell);
        if (in_array($normalizedCode, ['cod', 'cod.'], true)) {
            return true;
        }

        if ($codeCell !== '') {
            return false;
        }

        $normalizedPrice = $this->normalizeKey($priceCell);
        $normalizedBulk = $this->normalizeKey($bulkCell);

        return str_contains($normalizedPrice, 'lista')
            || str_contains($normalizedPrice, 'precio')
            || $normalizedBulk === 'bulto';
    }

    private function inferFamiliaId(string $titulo, array $codigos): int
    {
        $votes = [];

        foreach (array_unique($codigos) as $codigo) {
            foreach ($this->familyVotesByCode[$codigo] ?? [] as $familiaId => $count) {
                $votes[$familiaId] = ($votes[$familiaId] ?? 0) + $count;
            }
        }

        if ($votes !== []) {
            arsort($votes);

            return (int) array_key_first($votes);
        }

        $normalizedTitle = $this->normalizeKey($titulo);

        if (str_contains($normalizedTitle, 'yute') && isset($this->familyIdsBySlug['yute'])) {
            return $this->familyIdsBySlug['yute'];
        }

        if (str_contains($normalizedTitle, 'sisal') && isset($this->familyIdsBySlug['sisal'])) {
            return $this->familyIdsBySlug['sisal'];
        }

        return $this->familyIdsBySlug['cordones'] ?? array_values($this->familyIdsBySlug)[0];
    }

    private function upsertRubro(string $titulo, int $orden): array
    {
        $rubro = Rubro::firstOrNew(['titulo' => $titulo]);
        $wasCreated = ! $rubro->exists;

        $rubro->orden = $orden;

        $wasDirty = $rubro->isDirty();
        if ($wasCreated || $wasDirty) {
            $rubro->save();
        }

        return [$rubro, $wasCreated, ! $wasCreated && $wasDirty];
    }

    private function upsertCategoria(Rubro $rubro, string $titulo, int $familiaId, int $sheetOrder, int $categoryOrder): array
    {
        $categoria = CategoriaTodotex::firstOrNew([
            'titulo' => $titulo,
            'rubro_id' => $rubro->id,
        ]);

        $wasCreated = ! $categoria->exists;

        $categoria->familia_id = $familiaId;
        $categoria->orden = $this->buildOrder($sheetOrder, $categoryOrder);

        if ($wasCreated) {
            $categoria->visible = true;
            $categoria->destacado = false;
        }

        $wasDirty = $categoria->isDirty();
        if ($wasCreated || $wasDirty) {
            $categoria->save();
        }

        return [$categoria, $wasCreated, ! $wasCreated && $wasDirty];
    }

    private function attachProductsToCategoria(CategoriaTodotex $categoria, array $codigos): int
    {
        $productIds = $this->resolveProductIds($codigos);

        if ($productIds === []) {
            return 0;
        }

        $existingProductIds = DB::table('categoria_producto')
            ->where('categoria_id', $categoria->id)
            ->whereIn('producto_id', $productIds)
            ->pluck('producto_id')
            ->map(fn ($productId) => (int) $productId)
            ->all();

        $newProductIds = array_values(array_diff($productIds, $existingProductIds));

        if ($newProductIds !== []) {
            $categoria->productos()->attach($newProductIds);
        }

        return count($newProductIds);
    }

    private function resolveProductIds(array $codigos): array
    {
        $productIds = [];

        foreach (array_unique($codigos) as $codigo) {
            $productIds = array_merge(
                $productIds,
                $this->exactProductIdsByCode[$codigo] ?? [],
                $this->variantProductIdsByBaseCode[$codigo] ?? []
            );
        }

        return array_values(array_unique(array_map('intval', $productIds)));
    }

    private function buildOrder(int $sheetOrder, int $categoryOrder): string
    {
        return sprintf('ZZ-%02d-%03d', $sheetOrder, $categoryOrder);
    }

    private function normalizeText($value): string
    {
        $value = trim((string) $value);

        return preg_replace('/\s+/u', ' ', $value) ?? '';
    }

    private function normalizeKey(string $value): string
    {
        $value = mb_strtolower($this->normalizeText($value));

        return strtr($value, [
            'á' => 'a',
            'à' => 'a',
            'ä' => 'a',
            'â' => 'a',
            'é' => 'e',
            'è' => 'e',
            'ë' => 'e',
            'ê' => 'e',
            'í' => 'i',
            'ì' => 'i',
            'ï' => 'i',
            'î' => 'i',
            'ó' => 'o',
            'ò' => 'o',
            'ö' => 'o',
            'ô' => 'o',
            'ú' => 'u',
            'ù' => 'u',
            'ü' => 'u',
            'û' => 'u',
            'ñ' => 'n',
        ]);
    }
}
