<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sprawdzenie, czy administrator już istnieje
        $adminExists = DB::table('users')->where('login', 'admin')->exists();

        if (!$adminExists) {
            DB::table('users')->insert([
                'id' => Str::uuid(),
                'login' => 'admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('adminadmin'), // Haszowanie hasła
                'profile_picture' => 'img/profile/default/default_profile_picture.jpg',
                'role_id' => DB::table('roles')->where('name', 'admin')->value('id'),
                'created_at' => now(),
            ]);

            $this->command->info('✅ Administrator został dodany.');
        } else {
            $this->command->warn('⚠️ Administrator już istnieje – pominięto seedowanie.');
        }
    }
}
