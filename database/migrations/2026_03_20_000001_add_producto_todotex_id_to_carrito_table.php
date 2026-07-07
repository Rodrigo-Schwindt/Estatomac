<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carrito', function (Blueprint $table) {
            $table->unsignedBigInteger('producto_todotex_id')->nullable()->after('producto_id');
            $table->foreign('producto_todotex_id')->references('id')->on('productos_todotex')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('carrito', function (Blueprint $table) {
            $table->dropForeign(['producto_todotex_id']);
            $table->dropColumn('producto_todotex_id');
        });
    }
};
