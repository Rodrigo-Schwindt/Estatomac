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
        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->string('codigo_color', 4)->nullable()->after('codigo');
            $table->string('nombre_color')->nullable()->after('codigo_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->dropColumn(['codigo_color', 'nombre_color']);
        });
    }
};
