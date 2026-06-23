<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('site_changelogs', function (Blueprint $table) {
            $table->renameColumn('log_text', 'text');
        });

        Schema::table('site_changelogs', function (Blueprint $table) {
            $table->text('parsed_text')->nullable();
            $table->unsignedInteger('staff_id');
            $table->index(['type', 'type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('site_changelogs', function (Blueprint $table) {
            $table->dropIndex(['type', 'type_id']);
            $table->dropColumn('parsed_text');
            $table->dropColumn('staff_id');
        });

        Schema::table('site_changelogs', function (Blueprint $table) {
            $table->renameColumn('text', 'log_text');
        });
    }
};
