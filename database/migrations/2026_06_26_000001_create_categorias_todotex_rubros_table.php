<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_todotex_rubros', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_todotex_id');
            $table->unsignedBigInteger('rubro_id');
            $table->primary(['categoria_todotex_id', 'rubro_id']);
            $table->foreign('categoria_todotex_id')->references('id')->on('categorias_todotex')->onDelete('cascade');
            $table->foreign('rubro_id')->references('id')->on('rubros')->onDelete('cascade');
        });

        // Migrar datos existentes de la FK rubro_id a la pivot
        DB::table('categorias_todotex')
            ->whereNotNull('rubro_id')
            ->select(['id', 'rubro_id'])
            ->orderBy('id')
            ->chunk(500, function ($rows) {
                $inserts = $rows->map(fn ($r) => [
                    'categoria_todotex_id' => $r->id,
                    'rubro_id'             => $r->rubro_id,
                ])->toArray();
                DB::table('categorias_todotex_rubros')->insertOrIgnore($inserts);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_todotex_rubros');
    }
};
