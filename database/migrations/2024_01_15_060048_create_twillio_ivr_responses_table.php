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
        Schema::create('twillio_ivr_responses', function (Blueprint $table) {
            $table->id();
            $table->string('mobile');  
            $table->string('response_digit'); 
            $table->json('customer_options')->nullable();   
            $table->string('route_action')->nullable();  
            $table->unsignedInteger('attempts')->default(0);  
            $table->string('lang')->nullable(); 
            $table->string('dua_option')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twillio_ivr_responses');
    }
};
