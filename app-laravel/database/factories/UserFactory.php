<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid, // UUID jako klucz główny
            'login' => $this->faker->unique()->userName, // Unikalny login użytkownika
            'email' => $this->faker->unique()->safeEmail, // Unikalny email
            'password' => bcrypt('password'), // Domyślne hasło
            'profile_picture' => 'img/profile/default/default_profile_picture.jpg', // Domyślne zdjęcie profilowe
            'role_id' => Role::inRandomOrder()->first()->id ?? null, // Losowa rola z tabeli lub null, jeśli brak
            'created_at' => now(),
            'updated_at' => null,
        ];
    }
}
