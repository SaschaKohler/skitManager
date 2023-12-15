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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->string('avatar')->nullable();
            $table->text('color')->nullable()->default('rgb(255, 159, 64)');
            $table->text('title1')->nullable();
            $table->text('name1')->nullable();
            $table->text('name2')->nullable();
            $table->text('street')->nullable();
            $table->tinyText('country')->nullable();
            $table->text('zip')->nullable();
            $table->tinyText('city')->nullable();
            $table->text('phone1')->nullable();
            $table->text('fax1')->nullable();
            $table->text('phone2')->nullable();
            $table->text('konto')->nullable();
            $table->integer('blz')->nullable();
            $table->text('bank')->nullable();
            $table->text('title2')->nullable();
            $table->text('manager')->nullable();
            $table->smallInteger('nfaellig')->nullable();
            $table->smallInteger('skonto')->nullable();
            $table->text('preisgrp')->nullable();
            $table->smallInteger('role_id')->nullable()->default(6);
            $table->smallInteger('km')->nullable();
            $table->text('email1')->nullable();
            $table->text('www')->nullable();
            $table->string('dob')->nullable();
            $table->text('datev')->nullable();
            $table->text('uident')->nullable();
            $table->text('email')->nullable();
            $table->text('password')->nullable();
            $table->text('iban')->nullable();
            $table->text('bic')->nullable();
            $table->text('banknr')->nullable();
            $table->text('phone3')->nullable();
            $table->text('phone4')->nullable();
            $table->text('fax2')->nullable();
            $table->index(['name1']);
            $table->rememberToken()->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
};
