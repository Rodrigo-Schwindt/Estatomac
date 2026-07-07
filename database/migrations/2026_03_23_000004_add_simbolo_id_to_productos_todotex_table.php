<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->unsignedBigInteger('simbolo_id')->nullable()->after('id');
            $table->foreign('simbolo_id')->references('id')->on('simbolos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('productos_todotex', function (Blueprint $table) {
            $table->dropForeign(['simbolo_id']);
            $table->dropColumn('simbolo_id');
        });
    }
};
