<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVenueStateCitiesTable extends Migration
{
    public function up()
    {
        Schema::create('venue_state_cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venue_id'); 
            $table->string('city_name')->nullable();
            $table->string('city_image')->nullable();
            $table->string('state_name')->nullable();
            $table->string('columns_to_show')->nullable(); 
            $table->unsignedBigInteger('city_sequence_to_show')->nullable();
            $table->string('combination_name')->nullable();
            $table->timestamps(); 
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('venue_state_cities');
    }
}

