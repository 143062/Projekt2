<?php

namespace App\Repositories;

use PDO;
use PDOException;

class NoteRepository
{
    private $pdo;
    private $lastError;

    public function __construct()
    {
        $dsn = 'pgsql:host=db;port=5432;dbname=notatki_db;';
        $username = 'user';
        $password = 'password';
        $this->pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $this->lastError = null;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function saveNote($userId, $id, $title, $content)
    {
        try {
            if ($id) {
                $stmt = $this->pdo->prepare('UPDATE notes SET title = :title, content = :content WHERE id = :id AND user_id = :user_id');
                $params = ['id' => $id, 'user_id' => $userId, 'title' => $title, 'content' => $content];
                $stmt->execute($params);
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO notes (user_id, title, content) VALUES (:user_id, :title, :content) RETURNING id');
                $params = ['user_id' => $userId, 'title' => $title, 'content' => $content];
                $stmt->execute($params);
                $id = $stmt->fetchColumn();
            }
            return $id;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function clearSharedNotes($noteId)
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM shared_notes WHERE note_id = :note_id');
            $stmt->execute(['note_id' => $noteId]);
        } catch (PDOException $e) {
            // Obsługa błędu
        }
    }

    public function shareNoteWithUser($noteId, $sharedWithUserId)
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO shared_notes (note_id, shared_with_user_id) VALUES (:note_id, :shared_with_user_id)');
            $stmt->execute(['note_id' => $noteId, 'shared_with_user_id' => $sharedWithUserId]);
        } catch (PDOException $e) {
            // Obsługa błędu
        }
    }

    public function getNotesByUserId($userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM notes WHERE user_id = :user_id ORDER BY created_at ASC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSharedNotesWithUser($userId)
    {
        $stmt = $this->pdo->prepare('
            SELECT n.*, u.login AS owner_login
            FROM notes n
            JOIN shared_notes sn ON n.id = sn.note_id
            JOIN users u ON u.id = n.user_id
            WHERE sn.shared_with_user_id = :user_id
            ORDER BY n.created_at ASC
        ');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getSharedUsersByNoteId($noteId)
    {
        $stmt = $this->pdo->prepare('
            SELECT u.id, u.login, u.profile_picture 
            FROM shared_notes sn
            JOIN users u ON sn.shared_with_user_id = u.id
            WHERE sn.note_id = :note_id
        ');
        $stmt->execute(['note_id' => $noteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNoteById($noteId, $userId)
    {
        $stmt = $this->pdo->prepare('
            SELECT n.*
            FROM notes n
            LEFT JOIN shared_notes sn ON n.id = sn.note_id
            WHERE n.id = :note_id 
            AND (n.user_id = :user_id OR sn.shared_with_user_id = :user_id)
        ');
        $stmt->execute(['note_id' => $noteId, 'user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
