<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHousingZonesTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('housing_zones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('decor_id');
            $table->string('name');
            $table->integer('sort')->default(0);
            $table->boolean('has_mask')->default(0);
            $table->string('hash')->nullable();
            $table->string('svg_selector')->nullable();
            $table->boolean('allow_free_color')->default(1);

            $table->foreign('decor_id')->references('id')->on('housing_decors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('housing_zones');
    }
}
