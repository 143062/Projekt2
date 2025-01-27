<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FriendsSeeder extends Seeder
{
    public function run()
    {
        $userId = DB::table('users')->where('login', 'user1')->value('id');
        $friendId = DB::table('users')->where('login', 'admin1')->value('id');

        DB::table('friends')->insert([
            [
                'user_id' => $userId,
                'friend_id' => $friendId,
            ],
        ]);
    }
}
