<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        // Lista możliwych nazw ról
        $roleNames = ['admin', 'user'];

        // Sprawdzenie, które role już istnieją
        $existingRoles = Role::pluck('name')->toArray();
        $availableRoles = array_diff($roleNames, $existingRoles);

        // Jeśli nie ma ról do dodania, zwróć istniejące role
        if (empty($availableRoles)) {
            dump('Role(s) already exist: ', $existingRoles); // Wyświetlenie istniejących ról
            return [];
        }

        return [
            'id' => $this->faker->uuid, // UUID jako klucz główny
            'name' => array_shift($availableRoles), // Pobranie pierwszej dostępnej roli
        ];
    }
}
