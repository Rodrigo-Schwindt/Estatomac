<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carrito', function (Blueprint $table) {
            $table->integer('cantidad_presentacion')->default(1)->after('cantidad');
            $table->integer('cantidad_bultos')->default(0)->after('cantidad_presentacion');
        });

        $items = DB::table('carrito')
            ->leftJoin('productos_todotex', 'carrito.producto_todotex_id', '=', 'productos_todotex.id')
            ->select('carrito.id', 'carrito.cantidad', 'productos_todotex.bulto_cantidad')
            ->get();

        foreach ($items as $item) {
            $paso = max(1, (int) ($item->bulto_cantidad ?: 1));
            $cantidadPresentacion = max($paso, (int) $item->cantidad);

            DB::table('carrito')
                ->where('id', $item->id)
                ->update([
                    'cantidad_presentacion' => $cantidadPresentacion,
                    'cantidad_bultos' => 0,
                    'cantidad' => $cantidadPresentacion,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('carrito', function (Blueprint $table) {
            $table->dropColumn(['cantidad_presentacion', 'cantidad_bultos']);
        });
    }
};
