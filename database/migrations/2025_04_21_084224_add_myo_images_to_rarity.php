<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('rarities', function (Blueprint $table) {
            $table->boolean('has_myo_image')->default(0)->after('has_image');
            $table->string('myo_hash', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('rarities', function (Blueprint $table) {
            $table->dropColumn('has_myo_image');
            $table->dropColumn('myo_hash');
        });
    }
};
