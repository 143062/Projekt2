<?php

namespace App\Repositories;

use PDO;
use PDOException;

class UserRepository
{
    private $pdo;

    public function __construct()
    {
        $dsn = 'pgsql:host=db;port=5432;dbname=notatki_db;';
        $username = 'user';
        $password = 'password';
        $this->pdo = new PDO($dsn, $username, $password);
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function login($login, $password)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE login = :login');
        $stmt->execute(['login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function register($email, $login, $password)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT id FROM Roles WHERE name = :role_name');
            $stmt->execute(['role_name' => 'user']);
            $roleId = $stmt->fetchColumn();

            if ($roleId === false) {
                throw new PDOException('Nie znaleziono roli "user" w tabeli Roles.');
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare('INSERT INTO Users (email, login, password, role_id) VALUES (:email, :login, :password, :role_id)');
            $stmt->execute([
                'email' => $email,
                'login' => $login,
                'password' => $hashedPassword,
                'role_id' => $roleId
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Błąd podczas dodawania użytkownika: " . $e->getMessage());
            return false;
        }
    }

    public function addUser($email, $login, $password)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT id FROM Roles WHERE name = :role_name');
            $stmt->execute(['role_name' => 'user']);
            $roleId = $stmt->fetchColumn();

            if ($roleId === false) {
                throw new PDOException('Nie znaleziono roli "user" w tabeli Roles.');
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare('INSERT INTO Users (email, login, password, role_id) VALUES (:email, :login, :password, :role_id)');
            $stmt->execute([
                'email' => $email,
                'login' => $login,
                'password' => $hashedPassword,
                'role_id' => $roleId
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Błąd podczas dodawania użytkownika: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsersExcept($userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id != :user_id');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsers()
    {
        $stmt = $this->pdo->query('SELECT * FROM users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUserById($userId)
    {
        $stmt = $this->pdo->prepare('DELETE FROM Users WHERE id = :user_id');
        $stmt->execute(['user_id' => $userId]);
    }

    public function updateProfilePicture($userId, $profilePicturePath)
    {
        $stmt = $this->pdo->prepare('UPDATE Users SET profile_picture = :profile_picture WHERE id = :user_id');
        $stmt->execute([
            'profile_picture' => $profilePicturePath,
            'user_id' => $userId
        ]);
    }

    public function getUserById($userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Users WHERE id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
