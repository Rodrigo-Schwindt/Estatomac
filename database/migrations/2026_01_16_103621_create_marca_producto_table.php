<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('marca_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained()->onDelete('cascade');
            $table->foreignId('marca_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['producto_id', 'marca_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('marca_producto');
    }
};