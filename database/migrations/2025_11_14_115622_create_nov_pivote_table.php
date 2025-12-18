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
        Schema::create('nov_pivote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('nov_categories')->onDelete('cascade');
            $table->foreignId('novedades_id')->constrained('novedades')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['category_id', 'novedades_id']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('nov_pivote');
    }
};
