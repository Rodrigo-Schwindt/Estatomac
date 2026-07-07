<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categoria_producto', function (Blueprint $table) {
            $table->unsignedInteger('orden')->nullable()->after('producto_id');
        });
    }

    public function down(): void
    {
        Schema::table('categoria_producto', function (Blueprint $table) {
            $table->dropColumn('orden');
        });
    }
};
