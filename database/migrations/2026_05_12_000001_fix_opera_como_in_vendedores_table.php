<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendedores', function (Blueprint $table) {
            $table->dropColumn('opera_como');
        });

        Schema::table('vendedores', function (Blueprint $table) {
            $table->unsignedBigInteger('opera_como')->nullable()->after('comision');
            $table->foreign('opera_como', 'fk_vendedores_opera_como')
                  ->references('id')->on('vendedores')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vendedores', function (Blueprint $table) {
            $table->dropForeign('fk_vendedores_opera_como');
            $table->dropColumn('opera_como');
        });

        Schema::table('vendedores', function (Blueprint $table) {
            $table->string('opera_como')->nullable()->after('comision');
        });
    }
};
