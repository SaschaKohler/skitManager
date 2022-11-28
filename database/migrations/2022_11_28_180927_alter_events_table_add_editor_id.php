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
        Schema::table('events', function (Blueprint $table) {
            $table->bigInteger('editor_id')->nullable();
            $table->foreign('editor_id')
            ->references('id')->on('users')->cascadeOnDelete();
 $table->bigInteger('author_id')->nullable();
            $table->foreign('author_id')
            ->references('id')->on('users')->cascadeOnDelete();

        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('author_id');
        });
    }
};
