<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Parámetros de seguridad adicionales
        Schema::table('parametros', function (Blueprint $table) {
            $table->integer('int_fallidos')->default(5)->after('email_soporte');
            $table->integer('delay_error')->default(300)->after('int_fallidos');  // segundos de bloqueo
            $table->integer('dias_passwd')->default(0)->after('delay_error');     // 0 = sin expiración
            $table->integer('largo_passwd')->default(6)->after('dias_passwd');
        });

        // Tracking de cambio de contraseña
        Schema::table('clientes', function (Blueprint $table) {
            $table->timestamp('password_changed_at')->nullable()->after('password');
        });

        Schema::table('vendedores', function (Blueprint $table) {
            $table->timestamp('password_changed_at')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->dropColumn(['int_fallidos', 'delay_error', 'dias_passwd', 'largo_passwd']);
        });
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('password_changed_at');
        });
        Schema::table('vendedores', function (Blueprint $table) {
            $table->dropColumn('password_changed_at');
        });
    }
};
