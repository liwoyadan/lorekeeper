<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnedDecorsTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('owned_decors', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('decor_id');
            $table->text('customization')->nullable();
            $table->integer('count')->unsigned()->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('decor_id')->references('id')->on('housing_decors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('owned_decors');
    }
}
