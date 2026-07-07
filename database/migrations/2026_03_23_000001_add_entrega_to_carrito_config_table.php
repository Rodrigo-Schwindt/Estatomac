<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carrito_config', function (Blueprint $table) {
            $table->string('entrega_1_label')->default('Retiro cliente')->after('iva');
            $table->decimal('entrega_1_costo', 10, 2)->default(0)->after('entrega_1_label');
            $table->string('entrega_2_label')->default('Reparto TodoTex')->after('entrega_1_costo');
            $table->decimal('entrega_2_costo', 10, 2)->default(0)->after('entrega_2_label');
            $table->string('entrega_3_label')->default('Transporte / Expreso')->after('entrega_2_costo');
            $table->decimal('entrega_3_costo', 10, 2)->default(0)->after('entrega_3_label');
        });
    }

    public function down(): void
    {
        Schema::table('carrito_config', function (Blueprint $table) {
            $table->dropColumn([
                'entrega_1_label', 'entrega_1_costo',
                'entrega_2_label', 'entrega_2_costo',
                'entrega_3_label', 'entrega_3_costo',
            ]);
        });
    }
};
