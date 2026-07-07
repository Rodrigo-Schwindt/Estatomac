<?php

namespace Database\Seeders;

use App\Models\ColorTodotex;
use App\Models\ProductoTodotex;
use Illuminate\Database\Seeder;

class ColorTodotexFromProductsSeeder extends Seeder
{
    public function run(): void
    {
        $colores = ProductoTodotex::query()
            ->whereNotNull('codigo_color')
            ->select(['codigo_color', 'nombre_color'])
            ->orderBy('codigo_color')
            ->get()
            ->groupBy('codigo_color');

        $creadosOActualizados = 0;

        foreach ($colores as $codigoColor => $items) {
            $codigoColor = trim((string) $codigoColor);

            if ($codigoColor === '' || !preg_match('/^\d{4}$/', $codigoColor)) {
                continue;
            }

            $nombreColor = (string) $items
                ->pluck('nombre_color')
                ->filter(fn ($nombre) => filled($nombre))
                ->map(fn ($nombre) => trim((string) $nombre))
                ->first();

            if ($nombreColor === '') {
                $nombreColor = ProductoTodotex::COLOR_NAMES[$codigoColor] ?? null;
            }

            if (!$nombreColor) {
                continue;
            }

            ColorTodotex::updateOrCreate(
                ['codigo_color' => $codigoColor],
                [
                    'titulo' => $nombreColor,
                    'orden' => (int) $codigoColor,
                ]
            );

            $creadosOActualizados++;
        }

        $this->command?->info("Colores creados o actualizados: {$creadosOActualizados}");
    }
}
