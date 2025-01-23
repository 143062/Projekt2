<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function login($login, $password)
    {
        $user = DB::table('users')->where('login', $login)->first();

        if ($user && password_verify($password, $user->password)) {
            $userArray = (array) $user;

            // Usuwanie "public/" ze ścieżki zdjęcia profilowego
            if (isset($userArray['profile_picture'])) {
                $userArray['profile_picture'] = str_replace('public/', '', $userArray['profile_picture']);
            }

            return $userArray;
        }

        return false;
    }

    public function register($email, $login, $password)
    {
        try {
            $roleId = DB::table('roles')->where('name', 'user')->value('id');

            if (!$roleId) {
                throw new \Exception('Nie znaleziono roli "user" w tabeli Roles.');
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            DB::table('users')->insert([
                'email' => $email,
                'login' => $login,
                'password' => $hashedPassword,
                'role_id' => $roleId
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error("Błąd podczas rejestracji: " . $e->getMessage());
            return false;
        }
    }

    public function emailExists($email)
    {
        return DB::table('users')->where('email', $email)->exists();
    }

    public function loginExists($login)
    {
        return DB::table('users')->where('login', $login)->exists();
    }

    public function addUser($email, $username, $password, $role)
    {
        try {
            $roleId = DB::table('roles')->where('name', $role)->value('id');

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            DB::table('users')->insert([
                'email' => $email,
                'login' => $username,
                'password' => $hashedPassword,
                'role_id' => $roleId,
                'created_at' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error("Błąd podczas dodawania użytkownika: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsersWithRoles()
    {
        $users = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.id', 'users.login', 'users.email', 'users.created_at', 'roles.name as role', 'users.profile_picture')
            ->orderBy('users.created_at', 'asc')
            ->get()
            ->toArray();

        // Usuwanie "public/" ze ścieżek zdjęć profilowych
        foreach ($users as $user) {
            if (isset($user->profile_picture)) {
                $user->profile_picture = str_replace('public/', '', $user->profile_picture);
            }
        }

        return $users;
    }

    public function deleteUserById($userId)
    {
        try {
            DB::table('users')->where('id', $userId)->delete();
        } catch (\Exception $e) {
            throw new \Exception('Błąd podczas usuwania użytkownika: ' . $e->getMessage());
        }
    }

    public function updateProfilePicture($userId, $profilePicturePath)
    {
        DB::table('users')->where('id', $userId)->update([
            'profile_picture' => $profilePicturePath
        ]);
    }

    public function getUserById($userId)
    {
        Log::info('Pobieranie danych użytkownika dla user_id:', ['user_id' => $userId]);
    
        $user = DB::table('users')->where('id', $userId)->first();
    
        if ($user) {
            $userArray = (array) $user;
    
            // Usuwanie "public/" ze ścieżki zdjęcia profilowego
            if (isset($userArray['profile_picture'])) {
                $userArray['profile_picture'] = str_replace('public/', '', $userArray['profile_picture']);
            }
    
            Log::info('Dane użytkownika pobrane poprawnie:', $userArray);
            return $userArray;
        }
    
        Log::error('Nie znaleziono użytkownika w bazie danych dla user_id:', ['user_id' => $userId]);
        return null;
    }

    public function getUserByLogin($login)
    {
        $user = DB::table('users')->where('login', $login)->first();

        if ($user) {
            $userArray = (array) $user;

            // Usuwanie "public/" ze ścieżki zdjęcia profilowego
            if (isset($userArray['profile_picture'])) {
                $userArray['profile_picture'] = str_replace('public/', '', $userArray['profile_picture']);
            }

            return $userArray;
        }

        return null;
    }

    public function getUserIdByLogin($login)
    {
        return DB::table('users')->where('login', $login)->value('id');
    }

    public function isAdmin($userId)
    {
        return DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.id', $userId)
            ->where('roles.name', 'admin')
            ->exists();
    }

    public function changeUserPassword($userId, $hashedPassword)
    {
        DB::table('users')->where('id', $userId)->update([
            'password' => $hashedPassword
        ]);
    }

    public function updateUserPassword($userId, $hashedPassword)
    {
        try {
            DB::table('users')->where('id', $userId)->update([
                'password' => $hashedPassword
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error("Błąd podczas aktualizacji hasła: " . $e->getMessage());
            return false;
        }
    }

    public function getAdminRoleId()
    {
        return DB::table('roles')->where('name', 'admin')->value('id');
    }





#testowo

public function getFreshUserData($userId)
{
    $user = DB::table('users')
        ->where('id', $userId)
        ->select('id', 'login', 'email', 'profile_picture', 'role_id')
        ->first();

    if ($user && isset($user->profile_picture)) {
        $user->profile_picture = str_replace('public/', '', $user->profile_picture);
    }

    return $user ? (array)$user : null;
}






}





