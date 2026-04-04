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
            $table->boolean('character_1_featured')->default(false);
            $table->boolean('character_2_featured')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('character_relations', function (Blueprint $table) {
            $table->dropColumn('character_1_featured');
            $table->dropColumn('character_2_featured');
        });
    }
};
