<?php

namespace Database\Seeders;

use App\Models\ProductoTodotex;
use Illuminate\Database\Seeder;

class ProductoTodotexColorSeeder extends Seeder
{
    public function run(): void
    {
        $actualizados = 0;

        ProductoTodotex::query()
            ->select(['id', 'codigo', 'codigo_color', 'nombre_color'])
            ->chunkById(200, function ($productos) use (&$actualizados) {
                foreach ($productos as $producto) {
                    $colorData = ProductoTodotex::deriveColorData($producto->codigo);

                    if (
                        $producto->codigo_color !== $colorData['codigo_color'] ||
                        $producto->nombre_color !== $colorData['nombre_color']
                    ) {
                        $producto->forceFill([
                            'codigo_color' => $colorData['codigo_color'],
                            'nombre_color' => $colorData['nombre_color'],
                        ])->save();

                        $actualizados++;
                    }
                }
            });

        $this->command->info("Productos actualizados con color: {$actualizados}");
    }
}
