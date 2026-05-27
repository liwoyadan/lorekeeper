<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteChangelogsTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('site_changelogs', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->unsignedInteger('type_id')->nullable()->default(null);
            $table->text('log_text');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('staff_only')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('site_changelogs');
    }
}
