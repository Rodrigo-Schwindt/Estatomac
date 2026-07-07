<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Versión MINIMALISTA: agregamos solo lo indispensable para mapear el dump
 * del ERP a nuestras tablas sin romper datos del B2B.
 *
 * Decisiones:
 *  - clientes:  reemplazo total (100% del ERP) → traemos campos comerciales útiles
 *              + todas las FKs externas (para el contrato API ClientesPK = pk_externa).
 *  - productos: UPSERT por codigo → solo pk_externa, NO tocamos color/imágenes/categorización.
 *  - canales:   reemplazo total + porcentajes del ERP.
 *  - familias:  solo pk_externa por si en el futuro queremos asociar.
 *  - precios:   solo pk_externa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('pk_externa')->nullable()->index()->after('id');

            // Campos comerciales útiles del ERP
            $table->string('direccion_entregas', 255)->nullable()->after('domicilio');
            $table->string('horario_apertura', 255)->nullable()->after('direccion_entregas');
            $table->string('horario_cierre', 255)->nullable()->after('horario_apertura');
            $table->string('contactos', 255)->nullable()->after('horario_cierre');
            $table->text('observaciones_erp')->nullable()->after('contactos');
            $table->decimal('saldo', 18, 2)->nullable();
            $table->decimal('saldo_inicial', 18, 2)->nullable();
            $table->string('especial_sn', 1)->nullable();
            $table->string('nomina', 30)->nullable();
            $table->string('transporte_alternativo', 60)->nullable();
            $table->string('alerta', 255)->nullable();
            $table->string('radio', 60)->nullable();

            // FKs del ERP (los usamos para el contrato API: ClientesPK/CanalesPK/etc.)
            $table->unsignedInteger('ciudades_pk_externa')->nullable();
            $table->unsignedInteger('condiciones_iva_pk_externa')->nullable();
            $table->unsignedInteger('vendedores_pk_externa')->nullable()->index();
            $table->unsignedInteger('canales_pk_externa')->nullable()->index();
            $table->unsignedInteger('condiciones_ventas_pk_externa')->nullable();
            $table->unsignedInteger('tipos_operaciones_pk_externa')->nullable();
            $table->unsignedInteger('transportes_pk_externa')->nullable();
            $table->unsignedInteger('rubros_clientes_pk_externa')->nullable();
            $table->unsignedInteger('tipos_de_listas_pk_externa')->nullable();
        });

        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->unsignedBigInteger('pk_externa')->nullable()->index()->after('id');
        });

        Schema::table('canales', function (Blueprint $table) {
            $table->unsignedBigInteger('pk_externa')->nullable()->index()->after('id');
            $table->decimal('supervisor_pct', 18, 2)->nullable();
            $table->decimal('vendedor_pct', 18, 2)->nullable();
            $table->decimal('supervisor1_pct', 18, 2)->nullable();
            $table->decimal('supervisor2_pct', 18, 2)->nullable();
        });

        Schema::table('familias', function (Blueprint $table) {
            $table->unsignedBigInteger('pk_externa')->nullable()->index()->after('id');
        });

        Schema::table('precios', function (Blueprint $table) {
            $table->unsignedBigInteger('pk_externa')->nullable()->index()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'pk_externa', 'direccion_entregas', 'horario_apertura', 'horario_cierre',
                'contactos', 'observaciones_erp', 'saldo', 'saldo_inicial', 'especial_sn',
                'nomina', 'transporte_alternativo', 'alerta', 'radio',
                'ciudades_pk_externa', 'condiciones_iva_pk_externa', 'vendedores_pk_externa',
                'canales_pk_externa', 'condiciones_ventas_pk_externa', 'tipos_operaciones_pk_externa',
                'transportes_pk_externa', 'rubros_clientes_pk_externa', 'tipos_de_listas_pk_externa',
            ]);
        });
        Schema::table('productos_todotex', fn ($t) => $t->dropColumn('pk_externa'));
        Schema::table('canales', function (Blueprint $table) {
            $table->dropColumn(['pk_externa', 'supervisor_pct', 'vendedor_pct', 'supervisor1_pct', 'supervisor2_pct']);
        });
        Schema::table('familias', fn ($t) => $t->dropColumn('pk_externa'));
        Schema::table('precios', fn ($t) => $t->dropColumn('pk_externa'));
    }
};
