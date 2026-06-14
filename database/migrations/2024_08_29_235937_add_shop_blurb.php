<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('shops', function (Blueprint $table) {
            $table->text('blurb')->nullable()->default(null);
            $table->text('parsed_blurb')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('blurb');
            $table->dropColumn('parsed_blurb');
        });
    }
};
