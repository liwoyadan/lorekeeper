<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('comments', function (Blueprint $table) {
            $table->integer('character_id')->unsigned()->nullable()->default(null);
        });

        Schema::table('forums', function (Blueprint $table) {
            $table->boolean('characters_enabled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('character_id');
        });

        Schema::table('forums', function (Blueprint $table) {
            $table->dropColumn('characters_enabled');
        });
    }
};
