<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_book', function (Blueprint $table) {
            $table->id();
            $table->integer('u_id')->unsigned();
            $table->foreign('u_id')->references('u_id')->on('users');
            $table->integer('b_id')->unsigned();
            $table->foreign('b_id')->references('b_id')->on('books');
            $table->boolean('action_type')->default(1)
            ->nullable()
            ->comment('1=rented,2=return');
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
        Schema::dropIfExists('user_book');
    }
}
