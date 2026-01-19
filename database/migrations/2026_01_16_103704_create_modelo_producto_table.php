<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('modelo_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained()->onDelete('cascade');
            $table->foreignId('modelo_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['producto_id', 'modelo_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('modelo_producto');
    }
};