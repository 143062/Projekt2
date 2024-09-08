<?php

namespace App\Repositories;

use PDO;

class FriendRepository
{
    private $pdo;

    public function __construct()
    {
        $dsn = 'pgsql:host=db;port=5432;dbname=notatki_db;';
        $username = 'user';
        $password = 'password';
        $this->pdo = new PDO($dsn, $username, $password);
    }

    public function addFriend($userId, $friendId)
    {
        if ($this->isFriend($userId, $friendId)) {
            return false; 
        }

        $stmt = $this->pdo->prepare('INSERT INTO friends (user_id, friend_id) VALUES (:user_id, :friend_id)');
        $stmt->execute(['user_id' => $userId, 'friend_id' => $friendId]);

        return true;
    }

    public function isFriend($userId, $friendId)
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) 
            FROM friends 
            WHERE user_id = :user_id AND friend_id = :friend_id
        ');
        $stmt->execute(['user_id' => $userId, 'friend_id' => $friendId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getFriendsByUserId($userId)
    {
        // Dodanie zdjęcia profilowego do zwracanego zapytania
        $stmt = $this->pdo->prepare('
            SELECT users.id, users.login, users.email, users.profile_picture
            FROM users 
            JOIN friends ON users.id = friends.friend_id 
            WHERE friends.user_id = :user_id
        ');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getUserIdByLogin($login)
    {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE login = :login');
        $stmt->execute(['login' => $login]);
        return $stmt->fetchColumn();
    }

    public function deleteFriend($userId, $friendId)
    {
        $stmt = $this->pdo->prepare('DELETE FROM friends WHERE user_id = :user_id AND friend_id = :friend_id');
        $stmt->execute(['user_id' => $userId, 'friend_id' => $friendId]);
        
        // Zwracamy szczegółowe logi do konsoli przeglądarki
        $rowCount = $stmt->rowCount();
        return $rowCount > 0 ? ['rowCount' => $rowCount, 'log' => "Usunięto $rowCount wierszy dla użytkownika $userId i znajomego $friendId"] : ['rowCount' => 0, 'log' => "Nie udało się usunąć znajomego $friendId dla użytkownika $userId"];
    }
    
    
}
