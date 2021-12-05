<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('u_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('mobile')->length(10)->unique();
            $table->string('email')->unique();  
            $table->addColumn('tinyInteger', 'age', ['length' => 3]);
            $table->enum('gender', ['m', 'f','o']);
            $table->string('city');
            $table->string('password');
            $table->timestamps();
        });

        DB::table('users')->insert([
            [
                'firstname'    => 'admin', 
                'lastname'     => 'admin',
                'mobile'     => '9874563210', 
                'email' => 'admin@gmail.com',
                'age' => 35,
                'gender' => 'm',
                'city' => 'Mumbai',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            
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
        Schema::dropIfExists('users');
    }
}
