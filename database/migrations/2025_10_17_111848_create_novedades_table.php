<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('novedades', function (Blueprint $table) {
            $table->id();
            $table->string('image_banner')->nullable();            
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();    
            $table->string('orden')->nullable();        
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novedades');
    }
};
