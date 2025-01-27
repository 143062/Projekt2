<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class UserRepository
{
    public function login($login, $password)
    {
        $user = User::where('login', $login)->first();

        if ($user && password_verify($password, $user->password)) {
            // Usunięcie "public/" ze ścieżki zdjęcia profilowego
            if ($user->profile_picture) {
                $user->profile_picture = str_replace('public/', '', $user->profile_picture);
            }

            return $user->toArray();
        }

        return false;
    }

    public function register($email, $login, $password)
    {
        try {
            $role = Role::where('name', 'user')->first();

            if (!$role) {
                throw new \Exception('Nie znaleziono roli "user" w tabeli Roles.');
            }

            $hashedPassword = bcrypt($password);

            User::create([
                'email' => $email,
                'login' => $login,
                'password' => $hashedPassword,
                'role_id' => $role->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Błąd podczas rejestracji: " . $e->getMessage());
            return false;
        }
    }

    public function emailExists($email)
    {
        return User::where('email', $email)->exists();
    }

    public function loginExists($login)
    {
        return User::where('login', $login)->exists();
    }

    public function getAllUsersWithRoles()
    {
        return User::with('role')
            ->select('id', 'login', 'email', 'profile_picture', 'created_at', 'role_id')
            ->get()
            ->map(function ($user) {
                // Usunięcie "public/" ze ścieżki zdjęcia profilowego
                if ($user->profile_picture) {
                    $user->profile_picture = str_replace('public/', '', $user->profile_picture);
                }
                return $user;
            });
    }

    public function deleteUserById($userId)
    {
        User::findOrFail($userId)->delete();
    }

    public function updateProfilePicture($userId, $profilePicturePath)
    {
        $user = User::findOrFail($userId);
        $user->profile_picture = $profilePicturePath;
        $user->save();
    }

    public function getUserById($userId)
    {
        $user = User::find($userId);

        if ($user) {
            // Usunięcie "public/" ze ścieżki zdjęcia profilowego
            if ($user->profile_picture) {
                $user->profile_picture = str_replace('public/', '', $user->profile_picture);
            }
        }

        return $user ? $user->toArray() : null;
    }

    public function isAdmin($userId)
    {
        $user = User::find($userId);
        return $user && $user->role->name === 'admin';
    }

    public function changeUserPassword($userId, $hashedPassword)
    {
        $user = User::findOrFail($userId);
        $user->password = $hashedPassword;
        $user->save();
    }
}
