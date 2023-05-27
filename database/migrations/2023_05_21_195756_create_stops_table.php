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
        Schema::create('stops', function (Blueprint $table) {
            $table->id();

            $table->string('stop_id')->nullable();
            $table->string('stop_name')->nullable();
            $table->string('stop_desc')->nullable();
            $table->double('stop_lat')->nullable();
            $table->double('stop_lon')->nullable();
            $table->string('location_type')->nullable();

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
        Schema::dropIfExists('stops');
    }
};
