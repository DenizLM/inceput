<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->string('label')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('timestamp')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('bike_accessible')->nullable();
            $table->string('wheelchair_accessible')->nullable();
            $table->string('x_provider')->nullable();
            $table->string('x_rand')->nullable();
            $table->string('speed')->nullable();
            $table->string('route_id')->nullable();
            $table->string('trip_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};
