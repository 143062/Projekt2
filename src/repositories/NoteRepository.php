<?php

namespace App\Repositories;

use PDO;
use PDOException;

class NoteRepository
{
    private $pdo;

    public function __construct()
    {
        $dsn = 'pgsql:host=db;port=5432;dbname=notatki_db;';
        $username = 'user';
        $password = 'password';
        $this->pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function saveNote($userId, $id, $title, $content)
    {
        try {
            if ($id) {
                // Jeśli ID istnieje, aktualizujemy notatkę
                $stmt = $this->pdo->prepare('UPDATE notes SET title = :title, content = :content WHERE id = :id AND user_id = :user_id');
                $params = ['id' => $id, 'user_id' => $userId, 'title' => $title, 'content' => $content];
                error_log("Executing SQL (UPDATE): " . $stmt->queryString . " with params: " . json_encode($params));
            } else {
                // Jeśli ID nie istnieje, dodajemy nową notatkę
                $stmt = $this->pdo->prepare('INSERT INTO notes (user_id, title, content) VALUES (:user_id, :title, :content)');
                $params = ['user_id' => $userId, 'title' => $title, 'content' => $content];
                error_log("Executing SQL (INSERT): " . $stmt->queryString . " with params: " . json_encode($params));
            }
            
            $stmt->execute($params);
            
            return $id ? $id : $this->pdo->lastInsertId(); // Zwracamy ID dodanej lub zaktualizowanej notatki
        } catch (PDOException $e) {
            // Bardziej szczegółowe logowanie błędu
            error_log("Błąd podczas zapisywania notatki: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function getNotesByUserId($userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM notes WHERE user_id = :user_id ORDER BY created_at ASC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function shareNoteWithUser($noteId, $sharedWithUserId)
    {
        $stmt = $this->pdo->prepare('INSERT INTO shared_notes (note_id, shared_with_user_id) VALUES (:note_id, :shared_with_user_id)');
        $stmt->execute(['note_id' => $noteId, 'shared_with_user_id' => $sharedWithUserId]);
    }

    public function getSharedNotesByUserId($userId)
    {
        $stmt = $this->pdo->prepare('
            SELECT notes.* 
            FROM notes 
            JOIN shared_notes ON notes.id = shared_notes.note_id 
            WHERE shared_notes.shared_with_user_id = :user_id
        ');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
