<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->unsignedInteger('bulto_cantidad')->default(1)->after('bulto');
        });
    }

    public function down(): void
    {
        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->dropColumn('bulto_cantidad');
        });
    }
};
