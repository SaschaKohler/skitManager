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
        Schema::create('infos', function (Blueprint $table) {
            $table->id();
            $table->date('dob')->nullable()->default(now());
            $table->string('mobile')->nullable()->default('+6595895857');
            $table->string('language')->nullable()->default('German');
            $table->string('gender')->nullable()->default('male');
            $table->json('contactOptions')->nullable()->default(
                json_encode(
                    ['Phone','Email']
                )
            );
            $table->string('addressLine1')->nullable()->default('Arbingstr. 12');
            $table->string('addressLine2')->nullable()->default('');
            $table->string('postcode')->nullable()->default('1146');
            $table->string('city')->nullable()->default('Feichting');
            $table->string('state')->nullable()->default('UpperAustria');
            $table->string('country')->nullable()->default('Austria');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infos');
    }
};
