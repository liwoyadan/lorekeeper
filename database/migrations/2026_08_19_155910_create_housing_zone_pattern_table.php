<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHousingZonePatternTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('housing_zone_pattern', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zone_id');
            $table->unsignedInteger('pattern_id');

            $table->foreign('zone_id')->references('id')->on('housing_zones')->onDelete('cascade');
            $table->foreign('pattern_id')->references('id')->on('housing_patterns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('housing_zone_pattern');
    }
}
