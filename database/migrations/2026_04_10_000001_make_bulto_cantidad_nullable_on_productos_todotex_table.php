<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE productos_todotex MODIFY bulto_cantidad INT UNSIGNED NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('productos_todotex')
            ->whereNull('bulto_cantidad')
            ->update(['bulto_cantidad' => 1]);

        DB::statement('ALTER TABLE productos_todotex MODIFY bulto_cantidad INT UNSIGNED NOT NULL DEFAULT 1');
    }
};
