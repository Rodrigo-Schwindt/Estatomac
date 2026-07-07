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
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('title2')->nullable();
            $table->text('description2')->nullable();
            $table->longText('informacion')->nullable();
            $table->longText('escribenos')->nullable();
            $table->decimal('contado', 5, 2)->default(0);
            $table->decimal('transferencia', 5, 2)->default(0);
            $table->decimal('corriente', 5, 2)->default(0);
            $table->decimal('iva', 5, 2)->default(21);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrito_config');
    }
};
