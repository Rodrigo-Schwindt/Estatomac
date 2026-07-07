<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos_todotex', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('orden')->nullable();
            $table->boolean('destacado')->default(false);
            $table->boolean('visible')->default(true);
            $table->text('descripcion')->nullable();
            $table->string('codigo')->nullable();
            $table->text('presentacion')->nullable();
            $table->decimal('precio_paquete', 10, 2)->nullable();
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->decimal('precio_kg', 10, 2)->nullable();
            $table->decimal('porcentaje_aumento', 5, 2)->nullable();
            $table->decimal('descuento', 5, 2)->nullable();
            $table->string('bulto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos_todotex');
    }
};
