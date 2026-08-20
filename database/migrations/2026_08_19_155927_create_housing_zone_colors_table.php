<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHousingZoneColorsTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('housing_zone_colors', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zone_id');
            $table->string('hex');
            $table->integer('sort')->default(0);

            $table->foreign('zone_id')->references('id')->on('housing_zones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('housing_zone_colors');
    }
}
