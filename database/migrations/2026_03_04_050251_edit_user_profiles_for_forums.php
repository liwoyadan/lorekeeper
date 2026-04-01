<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('forum_decor_id');
            $table->dropColumn('forum_decor_hash');
            $table->dropColumn('forum_decor_extension');

            $table->text('forum_decor')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('forum_decor');
            $table->unsignedInteger('forum_decor_id')->nullable()->after('forum_flair_id');
            $table->string('forum_decor_hash', 10)->nullable()->after('forum_decor_id');
            $table->string('forum_decor_extension', 5)->nullable()->default('png')->after('forum_decor_hash');
        });
    }
};
