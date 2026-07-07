<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // password en clientes debe poder ser NULL (importer del ERP no trae passwords,
        // el cliente las define en el primer login con el flujo de "password inicial")
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });

        // bulk_import_logs.mensaje: longtext (4GB) — los stack traces de error superan TEXT (64KB)
        Schema::table('bulk_import_logs', function (Blueprint $table) {
            $table->longText('mensaje')->nullable()->change();
        });
    }

    public function down(): void
    {
        // No revertimos para evitar romper datos existentes
    }
};
