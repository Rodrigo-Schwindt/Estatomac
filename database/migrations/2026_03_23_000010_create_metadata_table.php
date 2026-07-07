<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metadata', function (Blueprint $table) {
            $table->id();
            $table->string('section')->unique();
            $table->text('keywords');
            $table->string('description', 160);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metadata');
    }
};
