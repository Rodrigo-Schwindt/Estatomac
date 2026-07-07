<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rubros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->unsignedInteger('orden')->nullable();
            $table->timestamps();
        });

        Schema::table('categorias_todotex', function (Blueprint $table) {
            $table->foreignId('rubro_id')
                ->nullable()
                ->after('familia_id')
                ->constrained('rubros')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias_todotex', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rubro_id');
        });

        Schema::dropIfExists('rubros');
    }
};
