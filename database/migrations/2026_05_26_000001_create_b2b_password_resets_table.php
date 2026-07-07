<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('b2b_password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20);
            $table->string('email', 120);
            $table->string('token', 80);
            $table->timestamp('created_at')->nullable();

            $table->unique(['tipo', 'email']);
            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b2b_password_resets');
    }
};
