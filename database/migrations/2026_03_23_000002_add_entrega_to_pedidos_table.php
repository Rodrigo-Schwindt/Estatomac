<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('forma_entrega')->nullable()->after('forma_pago');
            $table->decimal('costo_entrega', 10, 2)->default(0)->after('forma_entrega');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['forma_entrega', 'costo_entrega']);
        });
    }
};
