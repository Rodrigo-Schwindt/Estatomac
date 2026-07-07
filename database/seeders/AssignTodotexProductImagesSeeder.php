<?php

namespace Database\Seeders;

use App\Models\ProductoGallery;
use App\Models\ProductoTodotex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AssignTodotexProductImagesSeeder extends Seeder
{
    private const PHOTO_DIRECTORY = 'fotostodotex';

    public function run(): void
    {
        $disk = Storage::disk('public');

        if (! $disk->exists(self::PHOTO_DIRECTORY)) {
            $this->command?->warn('No existe la carpeta storage/app/public/' . self::PHOTO_DIRECTORY);

            return;
        }

        $photosByCode = collect($disk->files(self::PHOTO_DIRECTORY))
            ->filter(fn (string $path) => preg_match('/\.jpe?g$/i', $path) === 1)
            ->map(fn (string $path) => $this->parsePhoto($path))
            ->filter()
            ->sort(fn (array $a, array $b) => $this->comparePhotos($a, $b))
            ->values()
            ->groupBy('code');

        $productsByCode = ProductoTodotex::query()
            ->select(['id', 'codigo'])
            ->whereNotNull('codigo')
            ->get()
            ->groupBy(fn (ProductoTodotex $producto) => trim((string) $producto->codigo))
            ->filter(fn (Collection $productos, string $code) => $code !== '');

        $existingImagesByProduct = ProductoGallery::query()
            ->select(['producto_id', 'image'])
            ->get()
            ->groupBy('producto_id')
            ->map(fn (Collection $images) => $images->pluck('image')->flip());

        $productsWithPortada = ProductoGallery::query()
            ->where('portada', true)
            ->pluck('producto_id')
            ->flip();

        $created = 0;
        $alreadyAssigned = 0;
        $matchedCodes = collect();
        $matchedProductIds = collect();
        $photoCodesWithoutProduct = collect();

        foreach ($photosByCode as $code => $photos) {
            $products = $productsByCode->get((string) $code);

            if (! $products) {
                $photoCodesWithoutProduct->push((string) $code);
                continue;
            }

            $matchedCodes->push((string) $code);

            foreach ($products as $producto) {
                $matchedProductIds->push($producto->id);

                $existingImages = $existingImagesByProduct->get($producto->id, collect());
                $hasPortada = $productsWithPortada->has($producto->id);

                foreach ($photos as $photo) {
                    if ($existingImages->has($photo['path'])) {
                        $alreadyAssigned++;
                        continue;
                    }

                    ProductoGallery::create([
                        'producto_id' => $producto->id,
                        'image' => $photo['path'],
                        'portada' => ! $hasPortada,
                    ]);

                    $created++;
                    $hasPortada = true;
                    $productsWithPortada->put($producto->id, true);
                    $existingImages->put($photo['path'], true);
                }

                $existingImagesByProduct->put($producto->id, $existingImages);
            }
        }

        $fixedPortadas = $this->ensurePortadas($matchedProductIds->unique(), $productsWithPortada);
        $productCodesWithoutPhotos = $productsByCode->keys()->diff($photosByCode->keys());

        $this->command?->info('Fotos encontradas: ' . $photosByCode->flatten(1)->count());
        $this->command?->info('Codigos con fotos: ' . $photosByCode->keys()->count());
        $this->command?->info('Codigos con producto: ' . $matchedCodes->unique()->count());
        $this->command?->info('Productos alcanzados: ' . $matchedProductIds->unique()->count());
        $this->command?->info('Imagenes creadas: ' . $created);
        $this->command?->info('Imagenes que ya estaban asignadas: ' . $alreadyAssigned);
        $this->command?->info('Portadas corregidas: ' . $fixedPortadas);
        $this->command?->warn('Codigos de foto sin producto: ' . $photoCodesWithoutProduct->unique()->count());

        if ($photoCodesWithoutProduct->isNotEmpty()) {
            $this->command?->line($photoCodesWithoutProduct->unique()->sort()->values()->implode(', '));
        }

        $this->command?->warn('Productos sin fotos: ' . $productCodesWithoutPhotos->count());
    }

    private function parsePhoto(string $path): ?array
    {
        $name = basename($path);
        $baseName = pathinfo($name, PATHINFO_FILENAME);

        if (preg_match('/^(?<code>\d+)(?<suffix>.*)$/', $baseName, $matches) !== 1) {
            return null;
        }

        [$group, $number] = $this->parseSuffix($matches['suffix']);

        return [
            'path' => self::PHOTO_DIRECTORY . '/' . $name,
            'name' => $name,
            'code' => $matches['code'],
            'group' => $group,
            'number' => $number,
        ];
    }

    private function parseSuffix(string $suffix): array
    {
        if ($suffix === '') {
            return [0, 0];
        }

        if (preg_match('/^\.(\d+)$/', $suffix, $matches) === 1) {
            return [1, (int) $matches[1]];
        }

        if (preg_match('/^\s*\((\d+)\)$/', $suffix, $matches) === 1) {
            return [2, (int) $matches[1]];
        }

        return [3, PHP_INT_MAX];
    }

    private function comparePhotos(array $a, array $b): int
    {
        return ($a['group'] <=> $b['group'])
            ?: ($a['number'] <=> $b['number'])
            ?: strnatcasecmp($a['name'], $b['name']);
    }

    private function ensurePortadas(Collection $productIds, Collection $productsWithPortada): int
    {
        $fixed = 0;

        foreach ($productIds as $productId) {
            if ($productsWithPortada->has($productId)) {
                continue;
            }

            $firstImage = ProductoGallery::query()
                ->where('producto_id', $productId)
                ->orderBy('id')
                ->first();

            if (! $firstImage) {
                continue;
            }

            $firstImage->forceFill(['portada' => true])->save();
            $productsWithPortada->put($productId, true);
            $fixed++;
        }

        return $fixed;
    }
}
