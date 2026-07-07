<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colores_todotex', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('codigo_color', 4)->unique();
            $table->unsignedInteger('orden')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colores_todotex');
    }
};
