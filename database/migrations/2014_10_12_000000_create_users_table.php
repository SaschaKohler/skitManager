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
//            $table->string('fullName')->nullable()->default('Max Mustermann');
//            $table->string('username')->unique();
//            $table->string('password')->nullable();
//            $table->string('email')->unique();
//            $table->timestamp('email_verified_at')->nullable();
//            $table->text('role')->default('guest');
//            $table->text('status')->default('pending');

//            $table->json('ability')->nullable()->default(
//                json_encode([
//                        [ 'action' => 'read' , 'subject' => 'Auth'  ],
//                        [ 'action' => 'read' , 'subject' => 'ACL'  ],
//                    ]
//                ));
//            $table->json('permissionsData')->nullable()->default(
//                json_encode([
//                        [ 'module' => 'Admin'  , 'read' => 'true' , 'write' => 'false' , 'create' => 'false' ,'delete' => 'true' ],
//                        [ 'module' => 'Client'  , 'read' => 'true' , 'write' => 'false' , 'create' => 'false' ,'delete' => 'true' ],
//                        [ 'module' => 'Worker'  , 'read' => 'true' , 'write' => 'false' , 'create' => 'false' ,'delete' => 'true' ],
//                    ]
//                ));
//
//            $table->json('extras')->nullable();


            $table->text('search')->nullable();
            $table->text('title1')->nullable();
            $table->text('name1')->nullable();
            $table->text('name2')->nullable();
            $table->text('street')->nullable();
            $table->tinyText('country')->nullable();
            $table->text('plz')->nullable();
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
            $table->text('email')->nullable();
            $table->text('password')->nullable();
            $table->text('iban')->nullable();
            $table->text('bic')->nullable();
            $table->text('banknr')->nullable();
            $table->text('phone3')->nullable();
            $table->text('phone4')->nullable();
            $table->text('fax2')->nullable();
            $table->index(['search','name1']);
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
