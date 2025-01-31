<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sprawdzenie, czy użytkownik już istnieje
        $userExists = DB::table('users')->where('login', 'user')->exists();

        if (!$userExists) {
            DB::table('users')->insert([
                'id' => Str::uuid(),
                'login' => 'user',
                'email' => 'user@user.com',
                'password' => Hash::make('useruser'), // Haszowanie hasła
                'profile_picture' => 'img/profile/default/default_profile_picture.jpg',
                'role_id' => DB::table('roles')->where('name', 'user')->value('id'),
                'created_at' => now(),
            ]);

            $this->command->info('✅ Użytkownik został dodany.');
        } else {
            $this->command->warn('⚠️ Użytkownik już istnieje – pominięto seedowanie.');
        }
    }
}
