<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubro_producto_orden', function (Blueprint $table) {
            $table->unsignedBigInteger('rubro_id');
            $table->unsignedBigInteger('producto_id');
            $table->unsignedInteger('orden')->nullable();

            $table->primary(['rubro_id', 'producto_id']);

            $table->foreign('rubro_id')->references('id')->on('rubros')->cascadeOnDelete();
            $table->foreign('producto_id')->references('id')->on('productos_todotex')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rubro_producto_orden');
    }
};
