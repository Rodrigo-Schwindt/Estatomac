<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('precios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('archivo'); // Guardará la ruta del PDF o XLS
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('precios');
    }
};