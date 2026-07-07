<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canales', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->nullable()->unique();
            $table->string('canal', 60);
            $table->decimal('descuento_canal', 8, 2)->default(0);
            $table->timestamps();
            $table->index('canal');
        });

        Schema::create('condiciones_iva', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->nullable()->unique();
            $table->string('nombre', 60)->unique();
            $table->timestamps();
        });

        Schema::create('condiciones_ventas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->nullable()->unique();
            $table->string('nombre', 60)->unique();
            $table->timestamps();
        });

        Schema::create('tipos_operaciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->nullable()->unique();
            $table->string('nombre', 60)->unique();
            $table->timestamps();
        });

        Schema::create('transportes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->nullable()->unique();
            $table->string('nombre', 60)->unique();
            $table->timestamps();
        });

        Schema::create('rubros_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->nullable()->unique();
            $table->string('nombre', 60)->unique();
            $table->timestamps();
        });

        Schema::create('tipos_listas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->nullable()->unique();
            $table->string('nombre', 60)->unique();
            $table->timestamps();
        });

        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->nullable();
            $table->string('nombre', 60);
            $table->string('provincia', 60)->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->timestamps();
            $table->unique(['nombre', 'provincia']);
            $table->index('codigo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciudades');
        Schema::dropIfExists('tipos_listas');
        Schema::dropIfExists('rubros_clientes');
        Schema::dropIfExists('transportes');
        Schema::dropIfExists('tipos_operaciones');
        Schema::dropIfExists('condiciones_ventas');
        Schema::dropIfExists('condiciones_iva');
        Schema::dropIfExists('canales');
    }
};
