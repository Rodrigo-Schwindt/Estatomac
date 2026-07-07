<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('codigo')->nullable()->unique()->after('id');
            $table->string('nombre_fantasia')->nullable()->after('nombre');
            $table->string('celular')->nullable()->after('telefono');
            $table->string('whatsapp')->nullable()->after('celular');
            $table->string('codigo_postal')->nullable()->after('localidad');
            $table->string('condicion_iva')->nullable()->after('cuit');
            $table->string('condicion_venta')->nullable();
            $table->string('tipo_operacion')->nullable();
            $table->decimal('descuento', 8, 2)->nullable();
            $table->string('transporte')->nullable();
            $table->foreignId('vendedor_id')->nullable()->constrained('vendedores')->nullOnDelete();
            $table->string('rubro_cliente')->nullable();
            $table->string('tipo_lista')->nullable();
            $table->string('canal')->nullable();
            $table->decimal('descuento_canal', 8, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['vendedor_id']);
            $table->dropColumn([
                'codigo', 'nombre_fantasia', 'celular', 'whatsapp', 'codigo_postal',
                'condicion_iva', 'condicion_venta', 'tipo_operacion', 'descuento',
                'transporte', 'vendedor_id', 'rubro_cliente', 'tipo_lista', 'canal', 'descuento_canal',
            ]);
        });
    }
};
