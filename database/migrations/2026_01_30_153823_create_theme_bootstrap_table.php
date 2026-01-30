<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThemeBootstrapTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('theme_bootstraps', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->text('color_data')->nullable()->default(null);
            $table->text('theme_color_data')->nullable()->default(null);
            $table->text('style_data')->nullable()->default(null);
            $table->text('custom_scss_data')->nullable()->default(null);

            $table->boolean('has_scss')->default(0);
            $table->timestamps();
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->integer('theme_bootstrap_id')->unsigned()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('theme_bootstraps');

        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn('theme_bootstrap_id');
        });
    }
}
