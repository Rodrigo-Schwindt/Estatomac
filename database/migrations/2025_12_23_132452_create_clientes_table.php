<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('usuario')->unique();
            $table->string('password');
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('cuil')->nullable();
            $table->string('cuit')->nullable();
            $table->string('telefono')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('localidad')->nullable();
            $table->string('provincia')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};