<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_metadata_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetadataTable extends Migration
{
    public function up()
    {
        Schema::create('metadata', function (Blueprint $table) {
            $table->id();
            $table->string('section'); 
            $table->text('keywords');
            $table->text('description');
            $table->string('editor')->nullable();
            $table->timestamps();
            
            $table->unique('section'); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('metadata');
    }
}