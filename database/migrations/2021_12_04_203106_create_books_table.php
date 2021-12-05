<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('b_id');
            $table->string('book_name')->unique();
            $table->string('author');
            $table->string('cover_image');
            $table->timestamps();
        });

        DB::table('books')->insert([
            [
                'book_name'    => '3 Mistakes in my life', 
                'author'     => 'Chetan Bhagat',
                'cover_image'     => 'default.png', 
            ],
            [
                'book_name'    => '2 states', 
                'author'     => 'Chetan Bhagat',
                'cover_image'     => 'default.png', 
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
