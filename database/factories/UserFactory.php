<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
      
        return [
            'firstname'    =>$this->faker->name(), 
            'lastname'     => $this->faker->lastName(),
            'mobile'     => $this->faker->numerify('##########'), //$this->faker->mobileNumber(),
            
            'email' => $this->faker->unique()->safeEmail(),
            'age' => $this->faker->numberBetween($min = 18, $max = 100),
            'gender' => $this->faker->randomElement(['m','f','o']),
            'city' => 'Mumbai',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
