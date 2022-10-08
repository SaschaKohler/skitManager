<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->firstName() . ' ' . $this->faker->lastName();
        return [
            'search' => Str::upper($name),
            'name1' => $name,
            'email' => Str::lower(Str::remove(' ', $name)) . '' . $this->faker->randomDigit(). '@'. $this->faker->domainName(),
            'phone1' => $this->faker->phoneNumber(),
            'role_id' => $this->faker->randomElement([1,2,3]),
         //   'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
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
