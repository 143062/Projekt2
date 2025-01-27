<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotesSeeder extends Seeder
{
    public function run()
    {
        $userId = DB::table('users')->where('login', 'user1')->value('id');

        DB::table('notes')->insert([
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => $userId,
                'title' => 'Notatka 1',
                'content' => 'To jest treść notatki 1.',
                'created_at' => now(),
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => $userId,
                'title' => 'Notatka 2',
                'content' => 'To jest treść notatki 2.',
                'created_at' => now(),
            ],
        ]);
    }
}
