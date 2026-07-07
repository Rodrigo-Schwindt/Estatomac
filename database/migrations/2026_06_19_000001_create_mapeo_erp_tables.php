<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas para coordinar las familias/subfamilias del ERP con las
 * familias/categorías comerciales del B2B.
 *
 *  - erp_familias / erp_subfamilias: copias ocultas de las tablas del ERP.
 *    Sus nombres "feos" no se exponen al cliente — solo se ven en el panel admin.
 *
 *  - mapeos_erp_categoria: para cada subfamilia (o familia) del ERP, el admin
 *    define a qué familia y/o categoría del B2B corresponde.
 *
 *  - productos_todotex.subfamilias_pk_externa / familias_pk_externa: guardan
 *    las FKs del ERP por producto para poder aplicar el mapeo después.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_familias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pk_externa')->unique();
            $table->string('nombre', 120);
            $table->timestamps();
        });

        Schema::create('erp_subfamilias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pk_externa')->unique();
            $table->string('nombre', 120);
            $table->unsignedBigInteger('familias_pk_externa')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('mapeos_erp_categoria', function (Blueprint $table) {
            $table->id();
            $table->string('entidad_tipo', 20); // 'familia' | 'subfamilia'
            $table->unsignedBigInteger('entidad_pk_externa');
            // A qué corresponde en el B2B (uno, otro, o los dos)
            $table->unsignedBigInteger('familia_id')->nullable();   // FK a familias
            $table->unsignedBigInteger('categoria_id')->nullable(); // FK a categorias_todotex
            $table->timestamps();

            $table->unique(['entidad_tipo', 'entidad_pk_externa'], 'mapeo_erp_uniq');
            $table->index('familia_id');
            $table->index('categoria_id');
        });

        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->unsignedBigInteger('familias_pk_externa')->nullable()->index()->after('pk_externa');
            $table->unsignedBigInteger('subfamilias_pk_externa')->nullable()->index()->after('familias_pk_externa');
        });
    }

    public function down(): void
    {
        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->dropColumn(['familias_pk_externa', 'subfamilias_pk_externa']);
        });
        Schema::dropIfExists('mapeos_erp_categoria');
        Schema::dropIfExists('erp_subfamilias');
        Schema::dropIfExists('erp_familias');
    }
};
