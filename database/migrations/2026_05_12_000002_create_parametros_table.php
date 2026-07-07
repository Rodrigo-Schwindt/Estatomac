<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
            $table->boolean('en_mantenimiento')->default(false);
            $table->string('email_soporte', 120)->nullable();
        });

        // Insert the single config row
        DB::table('parametros')->insert([
            'en_mantenimiento' => false,
            'email_soporte'    => null,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('parametros');
    }
};
