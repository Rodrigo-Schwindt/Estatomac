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
            $table->string('numero_pedido', 20)->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->enum('forma_pago', ['contado', 'transferencia', 'cuenta_corriente']);
            $table->text('mensaje')->nullable();
            $table->string('archivo_path')->nullable();
            $table->string('archivo_nombre')->nullable();
            
            $table->decimal('subtotal_sin_descuento', 12, 2);
            $table->decimal('descuentos', 12, 2)->default(0);
            $table->decimal('porcentaje_descuento', 5, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('porcentaje_iva', 5, 2);
            $table->decimal('iva', 12, 2);
            $table->decimal('total', 12, 2);
            
            $table->date('fecha_compra');
            $table->date('fecha_entrega');
            $table->boolean('entregado')->default(false);
            $table->date('fecha_entregado')->nullable();
            
            $table->timestamps();
        });

        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('codigo_producto', 100);
            $table->string('nombre_producto', 255);
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento_unitario', 10, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
        Schema::dropIfExists('pedidos');
    }
};