<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_pedido')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('forma_pago');
            $table->text('mensaje')->nullable();
            $table->string('archivo_path')->nullable();
            $table->string('archivo_nombre')->nullable();
            $table->decimal('subtotal_sin_descuento', 12, 2)->default(0);
            $table->decimal('descuentos', 12, 2)->default(0);
            $table->decimal('porcentaje_descuento', 8, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('porcentaje_iva', 8, 2)->default(21);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->date('fecha_compra')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->date('fecha_entregado')->nullable();
            $table->boolean('entregado')->default(false);
            $table->timestamps();
        });

        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->string('codigo_producto')->nullable();
            $table->string('nombre_producto')->nullable();
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('descuento_unitario', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
        Schema::dropIfExists('pedidos');
    }
};
