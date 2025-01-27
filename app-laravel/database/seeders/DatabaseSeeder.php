<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Dodawanie ról tylko, jeśli tabela roles jest pusta
        if (DB::table('roles')->count() === 0) {
            $this->call(RolesSeeder::class);
        }

        // Dodawanie użytkowników tylko, jeśli tabela users jest pusta
        if (DB::table('users')->count() === 0) {
            $this->call(UsersSeeder::class);
        }

        // Dodawanie notatek tylko, jeśli tabela notes jest pusta
        if (DB::table('notes')->count() === 0) {
            $this->call(NotesSeeder::class);
        }

        // Dodawanie znajomych tylko, jeśli tabela friends jest pusta
        if (DB::table('friends')->count() === 0) {
            $this->call(FriendsSeeder::class);
        }

        // Dodawanie udostępnionych notatek tylko, jeśli tabela shared_notes jest pusta
        if (DB::table('shared_notes')->count() === 0) {
            $this->call(SharedNotesSeeder::class);
        }
    }
}
