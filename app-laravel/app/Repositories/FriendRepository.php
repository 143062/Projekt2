<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Log;

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

        try {
            // Dodanie znajomego do relacji
            $user = User::findOrFail($userId);
            $user->friends()->attach($friendId);

            return true;
        } catch (\Exception $e) {
            Log::error("Błąd podczas dodawania znajomego: " . $e->getMessage());
            return false;
        }
    }

    public function isFriend($userId, $friendId)
    {
        try {
            return User::findOrFail($userId)
                ->friends()
                ->where('friend_id', $friendId)
                ->exists();
        } catch (\Exception $e) {
            Log::error("Błąd podczas sprawdzania znajomości: " . $e->getMessage());
            return false;
        }
    }

    public function getFriendsByUserId($userId)
    {
        try {
            $friends = User::findOrFail($userId)
                ->friends()
                ->select('id', 'login', 'email', 'profile_picture')
                ->get();

            // Usunięcie "public/" ze ścieżek zdjęć profilowych znajomych
            return $friends->map(function ($friend) {
                if ($friend->profile_picture) {
                    $friend->profile_picture = str_replace('public/', '', $friend->profile_picture);
                }
                return $friend;
            });
        } catch (\Exception $e) {
            Log::error("Błąd podczas pobierania znajomych użytkownika: " . $e->getMessage());
            return [];
        }
    }

    public function getUserIdByLogin($login)
    {
        try {
            return User::where('login', $login)->value('id');
        } catch (\Exception $e) {
            Log::error("Błąd podczas pobierania ID użytkownika po loginie: " . $e->getMessage());
            return null;
        }
    }

    public function deleteFriend($userId, $friendId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->friends()->detach($friendId);

            Log::info("Usunięto znajomego $friendId dla użytkownika $userId");
            return true;
        } catch (\Exception $e) {
            Log::error("Błąd podczas usuwania znajomego: " . $e->getMessage());
            return false;
        }
    }
}
