<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('articles', function (Blueprint $table) {
            $table->decimal('vk1_perc', 10, 2)->nullable();
            $table->decimal('vk2_perc', 10, 2)->nullable();
            $table->decimal('vk3_perc', 10, 2)->nullable();
//
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('vk1_perc');
            $table->dropColumn('vk2_perc');
            $table->dropColumn('vk3_perc');
        });
    }
};
