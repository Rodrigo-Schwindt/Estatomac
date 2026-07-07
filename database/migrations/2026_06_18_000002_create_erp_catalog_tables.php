<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Solo creamos `listas_precios_importes` — los precios reales por producto.
 * Las demás tablas (subfamilias, materiales, tipos_de_productos, unidades,
 * productos_exclusividad, productos_datos_comerciales) NO se crean
 * porque el B2B no las usa para nada — son metadata interna del ERP.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listas_precios_importes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pk_externa')->unique();
            $table->unsignedBigInteger('listas_precios_pk_externa')->index();
            $table->unsignedBigInteger('productos_pk_externa')->index();
            $table->decimal('precio_paquete', 18, 2)->default(0);
            $table->decimal('precio_unitario', 18, 2)->default(0);
            $table->decimal('precio_kilo', 18, 2)->default(0);
            $table->decimal('costo_producto', 18, 2)->default(0);
            $table->timestamps();

            $table->unique(['listas_precios_pk_externa', 'productos_pk_externa'], 'lpi_lista_producto_uniq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listas_precios_importes');
    }
};
