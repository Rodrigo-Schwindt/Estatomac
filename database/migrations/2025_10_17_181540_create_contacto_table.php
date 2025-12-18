<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact', function (Blueprint $table) {
            $table->id();
            $table->string('direction_adm')->nullable();
            $table->string('phone_amd')->nullable();
            $table->string('maps_adm')->nullable();
            $table->string('frame_adm')->nullable();
            $table->string('mail_adm')->nullable();
            $table->string('direction_sale')->nullable();
            $table->string('phone_sale')->nullable();
            $table->string('maps_sale')->nullable();
            $table->string('frame_sale')->nullable();
            $table->string('wssp')->nullable();
            $table->string('facebook')->nullable();
            $table->string('insta')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('icono_1')->nullable();
            $table->string('icono_2')->nullable();
            $table->string('icono_3')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacto');
    }
};
