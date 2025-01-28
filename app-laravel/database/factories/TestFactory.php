<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
