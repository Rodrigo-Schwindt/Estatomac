<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_subcategoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('subcategoria_id')->constrained('subcategorias')->onDelete('cascade');
            $table->string('valor')->nullable(); // El valor que le asignas a cada subcategoría
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_subcategoria');
    }
};