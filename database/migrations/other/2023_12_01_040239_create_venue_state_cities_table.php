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
            $table->unsignedMediumInteger('country_id');
            $table->unsignedMediumInteger('state_id');
            $table->unsignedMediumInteger('city_id');
            $table->string('country_image')->nullable();
            $table->string('city_image')->nullable();
            $table->string('state_image')->nullable();
            $table->string('combination_name')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('venue_state_cities');
    }
}

