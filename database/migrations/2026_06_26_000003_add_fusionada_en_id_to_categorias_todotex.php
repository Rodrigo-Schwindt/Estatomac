<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias_todotex', function (Blueprint $table) {
            $table->unsignedBigInteger('fusionada_en_id')->nullable()->after('visible');
            $table->foreign('fusionada_en_id')
                  ->references('id')
                  ->on('categorias_todotex')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('categorias_todotex', function (Blueprint $table) {
            $table->dropForeign(['fusionada_en_id']);
            $table->dropColumn('fusionada_en_id');
        });
    }
};
