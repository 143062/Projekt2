<?php

namespace App\Controllers;

use App\Repositories\NoteRepository;
use App\Repositories\FriendRepository;

class NoteController
{
    private $noteRepository;
    private $friendRepository;

    public function __construct()
    {
        $this->noteRepository = new NoteRepository();
        $this->friendRepository = new FriendRepository();
    }

    public function dashboard()
    {
        $userId = $_SESSION['user_id'];
        $notes = $this->noteRepository->getNotesByUserId($userId);  // Notatki użytkownika
        $sharedNotes = $this->noteRepository->getSharedNotesWithUser($userId);  // Notatki udostępnione przez innych użytkowników

        // Przekazujemy zarówno notatki użytkownika, jak i notatki udostępnione
        include 'public/views/dashboard.php';
    }

    public function addNote()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $title = $data['title'] ?? 'Brak tytułu';
            $content = $data['content'] ?? 'Brak treści';
            $userId = $_SESSION['user_id'];
            $noteId = $data['id'] ?? null;

            $result = $this->noteRepository->saveNote($userId, $noteId, $title, $content);

            if ($result !== false) {
                if (isset($data['shared_with'])) {
                    $this->noteRepository->clearSharedNotes($result);
                    foreach ($data['shared_with'] as $friendId) {
                        $this->noteRepository->shareNoteWithUser($result, $friendId);
                    }
                }
                echo json_encode(['success' => true, 'id' => $result, 'message' => 'Notatka zapisana pomyślnie']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Błąd podczas zapisywania notatki. PDOException: ' . $this->noteRepository->getLastError()]);
            }
            exit();
        }
    }

    public function editNote()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $noteId = $_GET['id'];
            $userId = $_SESSION['user_id'];

            // Pobieranie notatki
            $note = $this->noteRepository->getNoteById($noteId, $userId);

            // Pobieranie przypisanych użytkowników
            $sharedUsers = $this->noteRepository->getSharedUsersByNoteId($noteId);

            // Zwrócenie danych w formacie JSON
            echo json_encode([
                'note' => $note,
                'sharedUsers' => $sharedUsers
            ]);
            exit();
        }
    }

    public function deleteNote()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $noteId = $data['id'] ?? null;
            $userId = $_SESSION['user_id'];

            if ($noteId) {
                $result = $this->noteRepository->deleteNoteById($noteId, $userId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Notatka została usunięta']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Błąd podczas usuwania notatki']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Nie znaleziono ID notatki']);
            }
            exit();
        }
    }
}
