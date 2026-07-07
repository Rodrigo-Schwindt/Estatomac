<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega pk_externa a vendedores para vincular con el ERP (Personal.xlsx → PersonalPK).
 * Idempotente: si la columna ya existe, no hace nada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vendedores') && !Schema::hasColumn('vendedores', 'pk_externa')) {
            Schema::table('vendedores', function (Blueprint $table) {
                $table->unsignedBigInteger('pk_externa')->nullable()->index()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vendedores', 'pk_externa')) {
            Schema::table('vendedores', function (Blueprint $table) {
                $table->dropColumn('pk_externa');
            });
        }
    }
};
