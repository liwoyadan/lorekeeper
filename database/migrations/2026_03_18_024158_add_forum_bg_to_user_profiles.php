<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('forum_bg_hash', 10)->nullable()->default(null);
            $table->string('forum_bg_extension', 5)->nullable()->default(null);
            $table->tinyInteger('forum_bg_opacity')->unsigned()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('forum_bg_hash');
            $table->dropColumn('forum_bg_extension');
            $table->dropColumn('forum_bg_opacity');
        });
    }
};
