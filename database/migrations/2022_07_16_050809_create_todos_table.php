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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->date('dueDate');
            $table->text('description');
            $table->json('tags');
            $table->boolean('isCompleted')->default(false);
            $table->boolean('isDeleted')->default(false);
            $table->boolean('isImportant')->default(false);
            $table->foreignId('assignee_id')->nullable()
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
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
        Schema::dropIfExists('todos');
    }
};
