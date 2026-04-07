<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('character_relations', function (Blueprint $table) {
            $table->integer('character_1_sort')->nullable()->default(null);
            $table->integer('character_2_sort')->nullable()->default(null);

            $table->text('info')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('character_relations', function (Blueprint $table) {
            $table->dropColumn('character_1_sort');
            $table->dropColumn('character_2_sort');

            $table->string('info')->nullable()->change();
        });
    }
};
