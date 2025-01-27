<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'login' => 'user1',
                'email' => 'user1@example.com',
                'password' => bcrypt('password1'), // Hasło zahashowane
                'profile_picture' => 'public/img/profile/default/default_profile_picture.jpg',
                'role_id' => DB::table('roles')->where('name', 'user')->value('id'),
                'created_at' => now(),
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'login' => 'admin1',
                'email' => 'admin1@example.com',
                'password' => bcrypt('password1'),
                'profile_picture' => 'public/img/profile/default/default_profile_picture.jpg',
                'role_id' => DB::table('roles')->where('name', 'admin')->value('id'),
                'created_at' => now(),
            ],
        ]);
    }
}
