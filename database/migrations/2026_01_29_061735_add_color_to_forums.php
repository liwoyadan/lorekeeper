<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('forums', function (Blueprint $table) {
            $table->string('color')->nullable()->default(null);
            $table->string('hash', 10)->nullable()->default(null)->after('has_image');

            $table->boolean('has_icon')->default(0);
            $table->string('icon_hash', 10)->nullable()->default(null);
            $table->string('icon_extension', 5)->nullable()->default('png');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('forums', function (Blueprint $table) {
            $table->dropColumn('color');
            $table->dropColumn('hash');
            $table->dropColumn('has_icon');
            $table->dropColumn('icon_hash');
            $table->dropColumn('icon_extension');
        });
    }
};
