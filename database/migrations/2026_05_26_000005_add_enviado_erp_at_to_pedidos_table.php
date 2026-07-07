<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->timestamp('enviado_erp_at')->nullable()->after('fecha_anulado');
            $table->string('erp_ip', 45)->nullable()->after('enviado_erp_at');
            $table->index('enviado_erp_at');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex(['enviado_erp_at']);
            $table->dropColumn(['enviado_erp_at', 'erp_ip']);
        });
    }
};
