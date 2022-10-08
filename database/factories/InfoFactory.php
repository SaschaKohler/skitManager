<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Info>
 */
class InfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            //
            'dob' => $this->faker->date(),
            'mobile' => $this->faker->phoneNumber(),
            'language' => $this->faker->languageCode(),
            'gender' => $this->faker->randomElement(['male','female']),
            'contactOptions' => ['Email','Phone','Message'] ,
            'addressLine1' => $this->faker->streetAddress(),
            'postcode' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'state' => $this->faker->randomElement(['UpperAustria','LowerAustria','Burgenland','Wien']),
            'country' => $this->faker->country()

        ];
    }
}
