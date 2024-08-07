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
        Schema::create('door_logs', function (Blueprint $table) {
            $table->id();
            $table->string('SN')->nullable();
            $table->uuid('SCode')->nullable();

            $table->string('DeviceID')->nullable();
            $table->string('ReaderNo')->nullable();
            $table->string('ActIndex')->nullable();

            $table->foreign('SCode')
                  ->references('booking_uniqueid')
                  ->on('visitors')
                  ->onDelete('set null');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('door_logs', function (Blueprint $table) {
            $table->dropForeign(['SCode']);
        });

        Schema::dropIfExists('door_logs');
    }
};

// php artisan migrate:refresh --path=/database/migrations/2024_07_31_063616_create_door_logs_table.php

