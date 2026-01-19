<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrito_config', function (Blueprint $table) {
            $table->id();
            $table->text('informacion')->nullable();
            $table->text('escribenos')->nullable();
            $table->decimal('contado', 5, 2)->default(10.00);
            $table->decimal('transferencia', 5, 2)->default(5.00);
            $table->decimal('corriente', 5, 2)->default(0.00);
            $table->decimal('iva', 5, 2)->default(21.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrito_config');
    }
};