<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carrito', function (Blueprint $table) {
            $table->decimal('descuento_personalizado', 8, 2)->default(0)->after('descuento_unitario');
        });

        Schema::table('pedido_items', function (Blueprint $table) {
            $table->decimal('descuento_base_porcentaje', 8, 2)->default(0)->after('descuento_unitario');
            $table->decimal('descuento_personalizado_porcentaje', 8, 2)->default(0)->after('descuento_base_porcentaje');
        });
    }

    public function down(): void
    {
        Schema::table('pedido_items', function (Blueprint $table) {
            $table->dropColumn(['descuento_base_porcentaje', 'descuento_personalizado_porcentaje']);
        });

        Schema::table('carrito', function (Blueprint $table) {
            $table->dropColumn('descuento_personalizado');
        });
    }
};
