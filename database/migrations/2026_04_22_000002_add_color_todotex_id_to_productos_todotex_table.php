<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->foreignId('color_todotex_id')
                ->nullable()
                ->after('codigo')
                ->constrained('colores_todotex')
                ->nullOnDelete();
        });

        DB::table('productos_todotex as productos')
            ->join('colores_todotex as colores', 'colores.codigo_color', '=', 'productos.codigo_color')
            ->whereNull('productos.color_todotex_id')
            ->update([
                'productos.color_todotex_id' => DB::raw('colores.id'),
                'productos.nombre_color' => DB::raw('colores.titulo'),
            ]);
    }

    public function down(): void
    {
        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->dropConstrainedForeignId('color_todotex_id');
        });
    }
};
