<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessibilityTables extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('accessibility_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key');
            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->string('input_type');
            $table->string('panel_key');

            $table->text('options_data')->nullable()->default(null);
            $table->text('default_value')->nullable()->default(null);

            $table->integer('sort_order')->default(0);
            $table->boolean('is_constrained')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('accessibility_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key');
            $table->text('selector')->nullable()->default(null);
            $table->string('property')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->text('accessibility_data')->nullable()->default(null);
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->text('accessibility_data')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('accessibility_settings');
        Schema::dropIfExists('accessibility_overrides');

        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn('accessibility_data');
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn('accessibility_data');
        });
    }
}
