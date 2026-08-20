<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLayoutToHomesTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('homes', function (Blueprint $table) {
            $table->text('layout')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('homes', function (Blueprint $table) {
            $table->dropColumn('layout');
        });
    }
}
