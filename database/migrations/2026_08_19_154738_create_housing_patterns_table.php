<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHousingPatternsTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('housing_patterns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('sort')->default(0);
            $table->boolean('has_image')->default(0);
            $table->string('hash')->nullable();
            $table->boolean('is_visible')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('housing_patterns');
    }
}
