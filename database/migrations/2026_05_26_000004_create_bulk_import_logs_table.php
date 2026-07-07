<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('proceso', 60);
            $table->string('archivo', 255)->nullable();
            $table->string('estado', 20);
            $table->integer('filas_procesadas')->default(0);
            $table->text('mensaje')->nullable();
            $table->json('detalle_errores')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['proceso', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_import_logs');
    }
};
