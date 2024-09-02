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

    public function addNote()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $title = $data['title'];
            $content = $data['content'];
            $userId = $_SESSION['user_id'];
            $noteId = $data['id'] ?? null;

            $result = $this->noteRepository->saveNote($userId, $noteId, $title, $content);

            if ($result) {
                echo json_encode(['success' => true, 'id' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Błąd podczas zapisywania notatki.']);
            }
            exit();
        }
    }

    public function dashboard()
    {
        $userId = $_SESSION['user_id'];
        $notes = $this->noteRepository->getNotesByUserId($userId);
        $sharedNotes = $this->noteRepository->getSharedNotesByUserId($userId);
        include 'public/views/dashboard.php';
    }
}
