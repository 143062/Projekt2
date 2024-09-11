<?php

namespace App\Tests;

use PDO;
use PDOException;

class TestHelper
{
    // Metoda zwracająca połączenie PDO
    public static function getPdo(): PDO
    {
        try {
            return new PDO('pgsql:host=db;port=5432;dbname=notatki_db', 'user', 'password', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
    }

    // Generowanie UUID
    public static function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    // Dodanie testowego użytkownika, jeśli nie istnieje
    public static function addTestUserIfNotExists(PDO $pdo, $userId, $login, $email): void
    {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingUser) {
            $stmt = $pdo->prepare('INSERT INTO users (id, login, email, password, role_id) VALUES (:id, :login, :email, :password, (SELECT id FROM roles WHERE name = :role))');
            $stmt->execute([
                'id' => $userId,
                'login' => $login,
                'email' => $email,
                'password' => password_hash('password', PASSWORD_BCRYPT),
                'role' => 'user'
            ]);
        }
    }

    // Usuwanie testowego użytkownika
    public static function deleteTestUser(PDO $pdo, $userId): void
    {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
    }
}