<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid, // UUID jako klucz główny
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(), // Losowy użytkownik lub fabryka użytkownika
            'title' => $this->faker->sentence(6), // Losowy tytuł
            'content' => $this->faker->paragraphs(3, true), // Losowa treść
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
