<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Calendar>
 */
class CalendarFactory extends Factory
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
            'type' => $this->faker->text(10) . '_' . $this->faker->randomDigitNotZero(),
=======
>>>>>>> fb1882d (refactoring)
            'description' => $this->faker->text(20),
            'color' => $this->faker->hexColor()
>>>>>>> 5849466 (updated a lot)
        ];
    }
}
