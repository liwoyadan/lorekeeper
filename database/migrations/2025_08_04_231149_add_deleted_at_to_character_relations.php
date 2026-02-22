<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('character_relations', function (Blueprint $table) {
            $table->softDeletes();
            $table->integer('user_item_id')->unsigned()->nullable()->default(null);

            $table->renameColumn('type', 'character_1_type');
            $table->string('character_2_type')->default('???');

            DB::statement('ALTER TABLE character_relations MODIFY COLUMN status ENUM( "Pending", "Approved", "Rejected" ) DEFAULT "Pending";');
        });

        Schema::create('relations_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('character_1_id')->unsigned();
            $table->integer('character_2_id')->unsigned();

            $table->integer('sender_id')->unsigned();
            $table->integer('recipient_id')->unsigned()->nullable();

            $table->integer('relation_id')->unsigned();
            $table->integer('stack_id')->unsigned()->nullable();

            $table->text('log');
            $table->string('log_type');
            $table->text('data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('character_relations', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropColumn('user_item_id');

            $table->renameColumn('character_1_type', 'type');
            $table->dropColumn('character_2_type');

            DB::statement('ALTER TABLE character_relations MODIFY COLUMN status ENUM( "Pending", "Approved" ) DEFAULT "Pending";');
        });
        Schema::dropIfExists('relations_log');
    }
};
