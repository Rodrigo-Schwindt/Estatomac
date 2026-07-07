<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega vendedor_operativo_id a pedidos.
 *
 * Distingue dos vendedores en el pedido:
 *  - vendedor_id: titular al que se imputa la venta (puede ser el opera_como).
 *  - vendedor_operativo_id: vendedor físico que cargó el pedido cuando opera
 *    en nombre de otro. NULL si el titular cargó por sí mismo.
 *
 * Idempotente.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pedidos') && !Schema::hasColumn('pedidos', 'vendedor_operativo_id')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->foreignId('vendedor_operativo_id')
                    ->nullable()
                    ->after('vendedor_id')
                    ->constrained('vendedores')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pedidos', 'vendedor_operativo_id')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->dropConstrainedForeignId('vendedor_operativo_id');
            });
        }
    }
};
