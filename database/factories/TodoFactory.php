<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            'title' => $this->faker->text(30),
            'dueDate' => $this->faker->date(),
            'description' => $this->faker->text(250),
            'tags' => $this->faker->randomElements([ 'update' , 'medium' ,'high' ])
            //
        ];
    }
}
