<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carrito_config', function (Blueprint $table) {
            $table->boolean('entrega_1_activa')->default(true)->after('entrega_1_costo');
            $table->boolean('entrega_2_activa')->default(true)->after('entrega_2_costo');
            $table->boolean('entrega_3_activa')->default(true)->after('entrega_3_costo');
            $table->boolean('contado_activo')->default(true)->after('contado');
            $table->boolean('transferencia_activa')->default(true)->after('transferencia');
            $table->boolean('corriente_activa')->default(true)->after('corriente');
        });
    }

    public function down(): void
    {
        Schema::table('carrito_config', function (Blueprint $table) {
            $table->dropColumn([
                'entrega_1_activa',
                'entrega_2_activa',
                'entrega_3_activa',
                'contado_activo',
                'transferencia_activa',
                'corriente_activa',
            ]);
        });
    }
};
