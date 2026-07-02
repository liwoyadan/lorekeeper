<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateThemeBootstrapTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('theme_bootstraps', function (Blueprint $table) {
            $table->text('custom_prepend')->nullable()->default(null);
            $table->text('custom_append')->nullable()->default(null);
            $table->boolean('is_default')->default(0);
            $table->string('hash', 10)->nullable()->default(null);
            $table->dropColumn('has_scss');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('theme_bootstraps', function (Blueprint $table) {
            $table->dropColumn('custom_prepend');
            $table->dropColumn('custom_append');
            $table->dropColumn('is_default');
            $table->dropColumn('hash');
            $table->boolean('has_scss')->default(0);
        });
    }
}
