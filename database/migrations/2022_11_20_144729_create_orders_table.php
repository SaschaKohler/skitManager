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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number', 32)->unique();
            $table->decimal('total_price', 12, 2)->nullable();
=======
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number', 32)->unique();
            $table->decimal('total_price', 12, 2)->nullable();
            $table->decimal('discount', 12, 2)->nullable();
>>>>>>> origin/master
            $table->enum('status', ['new', 'processing', 'shipped', 'delivered', 'cancelled'])->default('new');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
