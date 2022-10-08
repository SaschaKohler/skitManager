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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('url')->nullable()->default('');
            $table->string('backgroundColor')->nullable()->default('green');
            $table->string('borderColor')->nullable()->default('blue');
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->boolean('allDay')->default('false');
          //  $table->string('calendar')->nullable();
            $table->json('extendedProps')->nullable();
            $table->json('images')->nullable();
            $table->foreignId('user_id')->nullable()->references('id')
                ->on('users')->cascadeOnDelete();
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
        Schema::dropIfExists('events');
    }
};
