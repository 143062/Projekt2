<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\NoteRepository;
use App\Repositories\FriendRepository;

class NoteController extends Controller
{
    private $noteRepository;
    private $friendRepository;

    public function __construct(NoteRepository $noteRepository, FriendRepository $friendRepository)
    {
        $this->noteRepository = $noteRepository;
        $this->friendRepository = $friendRepository;
    }

    /*                                                                          TEORETYCZNIE OBSLUZONA PRZEZ USERCONTROLLER
    public function dashboard()
    {
        $userId = session('user_id');
        $notes = $this->noteRepository->getNotesByUserId($userId); // Notatki użytkownika
        $sharedNotes = $this->noteRepository->getSharedNotesWithUser($userId); // Notatki udostępnione

        return view('dashboard', [
            'notes' => $notes,
            'sharedNotes' => $sharedNotes
        ]);
    }
    */

    public function addNote(Request $request)
    {
        $title = $request->input('title', 'Brak tytułu');
        $content = $request->input('content', 'Brak treści');
        $userId = session('user_id');
        $noteId = $request->input('id', null);

        $result = $this->noteRepository->saveNote($userId, $noteId, $title, $content);

        if ($result !== false) {
            if ($request->has('shared_with')) {
                $this->noteRepository->clearSharedNotes($result);
                foreach ($request->input('shared_with') as $friendId) {
                    $this->noteRepository->shareNoteWithUser($result, $friendId);
                }
            }

            return response()->json([
                'success' => true,
                'id' => $result,
                'message' => 'Notatka zapisana pomyślnie'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Błąd podczas zapisywania notatki.'
        ]);
    }

    public function editNote(Request $request)
    {
        $noteId = $request->query('id');
        $userId = session('user_id');

        $note = $this->noteRepository->getNoteById($noteId, $userId);
        $sharedUsers = $this->noteRepository->getSharedUsersByNoteId($noteId);

        return response()->json([
            'note' => $note,
            'sharedUsers' => $sharedUsers
        ]);
    }

    public function deleteNote(Request $request)
    {
        $noteId = $request->input('id');
        $userId = session('user_id');

        if ($noteId) {
            $result = $this->noteRepository->deleteNoteById($noteId, $userId);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notatka została usunięta'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Błąd podczas usuwania notatki'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nie znaleziono ID notatki'
        ]);
    }
}
