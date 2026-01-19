<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->onDelete('set null');
            $table->foreignId('modelo_id')->nullable()->constrained('modelos')->onDelete('set null');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->onDelete('set null');
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias')->onDelete('set null');
            $table->string('title');
            $table->string('title2');
            $table->string('image')->nullable();
            $table->string('order')->nullable();
            $table->boolean('visible')->default(true);
            $table->enum('nuevo', ['nuevo', 'recambio'])->default('nuevo');
            $table->decimal('precio', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};