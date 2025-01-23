<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class FriendRepository
{
    public function addFriend($userId, $friendId)
    {
        if (empty($userId) || empty($friendId)) {
            return false;
        }

        // Sprawdzenie, czy użytkownicy są już znajomymi
        if ($this->isFriend($userId, $friendId)) {
            return false;
        }

        // Dodanie znajomego do bazy danych
        DB::table('friends')->insert([
            'user_id' => $userId,
            'friend_id' => $friendId,
        ]);

        return true;
    }

    public function isFriend($userId, $friendId)
    {
        return DB::table('friends')
            ->where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->exists();
    }

    public function getFriendsByUserId($userId)
    {
        $friends = DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.friend_id')
            ->where('friends.user_id', $userId)
            ->select('users.id', 'users.login', 'users.email', 'users.profile_picture')
            ->get()
            ->toArray();

        // Usuwanie "public/" ze ścieżki zdjęć profilowych znajomych
        foreach ($friends as $friend) {
            if (isset($friend->profile_picture)) {
                $friend->profile_picture = str_replace('public/', '', $friend->profile_picture);
            }
        }

        return $friends;
    }

    public function getUserIdByLogin($login)
    {
        return DB::table('users')->where('login', $login)->value('id');
    }

    public function deleteFriend($userId, $friendId)
    {
        $deleted = DB::table('friends')
            ->where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->delete();

        // Zwracanie szczegółowych logów
        return [
            'rowCount' => $deleted,
            'log' => $deleted > 0
                ? "Usunięto $deleted wierszy dla użytkownika $userId i znajomego $friendId"
                : "Nie udało się usunąć znajomego $friendId dla użytkownika $userId",
        ];
    }
}
