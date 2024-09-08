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

    public function emailExists($email)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function loginExists($login)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE login = :login');
        $stmt->execute(['login' => $login]);
        return $stmt->fetchColumn() > 0;
    }

    public function addUser($email, $username, $password, $role)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT id FROM Roles WHERE name = :role');
            $stmt->execute(['role' => $role]);
            $roleId = $stmt->fetchColumn();

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare('INSERT INTO Users (email, login, password, role_id, created_at) VALUES (:email, :login, :password, :role_id, NOW())');
            $stmt->execute([
                'email' => $email,
                'login' => $username,
                'password' => $hashedPassword,
                'role_id' => $roleId
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllUsersWithRoles()
    {
        $stmt = $this->pdo->prepare('
            SELECT Users.id, Users.login, Users.email, Users.created_at, Roles.name AS role
            FROM Users
            JOIN Roles ON Users.role_id = Roles.id
            ORDER BY Users.created_at ASC
        ');
        $stmt->execute();
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

    // Nowa metoda do pobierania użytkownika na podstawie loginu
    public function getUserByLogin($login)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE login = :login');
        $stmt->execute(['login' => $login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Nowa metoda sprawdzająca, czy użytkownik ma rolę "admin"
    public function isAdmin($userId)
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) 
            FROM users 
            JOIN roles ON users.role_id = roles.id
            WHERE users.id = :user_id AND roles.name = :role_name
        ');
        $stmt->execute(['user_id' => $userId, 'role_name' => 'admin']);
        return $stmt->fetchColumn() > 0;
    }

    public function changeUserPassword($userId, $hashedPassword)
    {
        $stmt = $this->pdo->prepare('UPDATE Users SET password = :password WHERE id = :user_id');
        $stmt->execute([
            'password' => $hashedPassword,
            'user_id' => $userId
        ]);
    }

    public function updateUserPassword($userId, $hashedPassword)
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE Users SET password = :password WHERE id = :user_id');
            $stmt->execute([
                'password' => $hashedPassword,
                'user_id' => $userId
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Błąd podczas aktualizacji hasła: " . $e->getMessage());
            return false;
        }
    }
}
