<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNosotrosTable extends Migration
{
    public function up()
    {
        Schema::create('nosotros', function (Blueprint $table) {
            $table->id();


            $table->string('title_home')->nullable();
            $table->text('description_home')->nullable();
            $table->string('image_home')->nullable();
            
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            

            $table->string('title_1')->nullable();
            $table->text('description_1')->nullable();
            $table->string('image_1')->nullable();
            
            $table->string('title_2')->nullable();
            $table->text('description_2')->nullable();
            $table->string('image_2')->nullable();
            
            $table->string('title_3')->nullable();
            $table->text('description_3')->nullable();
            $table->string('image_3')->nullable();
            
            $table->string('title_4')->nullable();
            $table->text('description_4')->nullable();
            $table->string('image_4')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nosotros');
    }
}