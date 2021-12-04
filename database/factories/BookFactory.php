<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_name' =>  $this->faker->unique()->word,
            'author' => $this->faker->name,
            'cover_image' => 'test.jpg',
        ];
    }
}
