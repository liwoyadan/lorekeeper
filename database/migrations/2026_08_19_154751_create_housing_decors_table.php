<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHousingDecorsTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('housing_decors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('sort')->default(0);
            $table->string('kind');
            $table->string('render_mode')->default('mask');
            $table->string('layer')->nullable();
            $table->text('description')->nullable();
            $table->text('parsed_description')->nullable();
            $table->decimal('default_scale', 5, 2)->default(1.00);
            $table->boolean('has_image')->default(0);
            $table->string('hash')->nullable();
            $table->boolean('is_visible')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('housing_decors');
    }
}
