<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->text('forum_signature')->nullable()->default(null);
            $table->text('parsed_forum_signature')->nullable()->default(null);
            $table->integer('forum_flair_id')->unsigned()->nullable()->default(null);
            $table->integer('forum_decor_id')->unsigned()->nullable()->default(null);
            $table->string('forum_decor_hash', 10)->nullable()->default(null);
            $table->string('forum_decor_extension', 5)->nullable()->default('png');
        });

        Schema::create('forum_flairs', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->integer('post_requirement')->unsigned()->nullable()->default(null);

            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->string('color')->nullable()->default(null);
            $table->text('data')->nullable()->default(null);

            $table->string('hash', 10)->nullable()->default(null);
            $table->string('extension', 5)->nullable()->default('png');
            $table->boolean('has_image')->default(0);

            $table->boolean('staff_only')->default(0);
            $table->boolean('is_default')->default(0);
            $table->boolean('is_visible')->default(1);
        });

        Schema::create('forum_decors', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('type')->nullable()->default(null);

            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->text('data')->nullable()->default(null);

            $table->string('hash', 10)->nullable()->default(null);
            $table->string('extension', 5)->nullable()->default('png');
            $table->boolean('has_image')->default(0);

            $table->boolean('staff_only')->default(0);
            $table->boolean('is_default')->default(0);
            $table->boolean('is_visible')->default(1);
        });

        Schema::create('user_forum_flairs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->integer('forum_flair_id')->unsigned();
        });

        Schema::create('user_forum_decors', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->integer('forum_decor_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('forum_signature');
            $table->dropColumn('parsed_forum_signature');
            $table->dropColumn('forum_flair_id');
            $table->dropColumn('forum_decor_id');
            $table->dropColumn('forum_decor_hash', 10);
            $table->dropColumn('forum_decor_extension', 5);
        });

        Schema::dropIfExists('forum_flairs');
        Schema::dropIfExists('forum_decors');
        Schema::dropIfExists('user_forum_flairs');
        Schema::dropIfExists('user_forum_decors');
    }
};
