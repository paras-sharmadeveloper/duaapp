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
        Schema::create('working_ladies', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('employer_name')->nullable();
            $table->string('place_of_work')->nullable();
            $table->string('employee_id_image')->nullable();
            $table->string('passport_photo')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->text('why_consider_you_as_working_lady')->nullable();
            $table->enum('is_active',['active','inactive'])->default('inactive');
            $table->string('qr_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_ladies');
    }
};
