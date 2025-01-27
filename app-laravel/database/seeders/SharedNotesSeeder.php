<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SharedNotesSeeder extends Seeder
{
    public function run()
    {
        $noteId = DB::table('notes')->where('title', 'Notatka 1')->value('id');
        $userId = DB::table('users')->where('login', 'admin1')->value('id');

        DB::table('shared_notes')->insert([
            [
                'note_id' => $noteId,
                'user_id' => $userId,
            ],
        ]);
    }
}
