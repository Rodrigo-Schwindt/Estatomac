<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Guardamos solo el string `bulto` por (lista, producto) que viene en
 * ListasPreciosOtrosDatos.xlsx. Lo usamos para sincronizar el campo `bulto`
 * de productos_todotex desde la lista vigente. NO importamos el resto del
 * archivo (márgenes, costos, etc.) — son datos sensibles que el B2B no necesita.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listas_precios_importes', function (Blueprint $table) {
            $table->string('bulto', 120)->nullable()->after('costo_producto');
        });
    }

    public function down(): void
    {
        Schema::table('listas_precios_importes', function (Blueprint $table) {
            $table->dropColumn('bulto');
        });
    }
};
