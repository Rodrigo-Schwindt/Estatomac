<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('precios', function (Blueprint $table) {
            $table->integer('numero')->nullable()->after('id');
            $table->date('fecha_desde')->nullable()->after('title');
            $table->boolean('vigente_sn')->default(false)->after('fecha_desde');
            $table->text('observaciones')->nullable()->after('vigente_sn');

            $table->index('vigente_sn');
        });
    }

    public function down(): void
    {
        Schema::table('precios', function (Blueprint $table) {
            $table->dropIndex(['vigente_sn']);
            $table->dropColumn(['numero', 'fecha_desde', 'vigente_sn', 'observaciones']);
        });
    }
};
