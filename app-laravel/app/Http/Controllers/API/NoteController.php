<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\NoteRepository;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    private $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    /**
     * Pobieranie listy notatek użytkownika.
     */
    public function index()
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            $notes = $this->noteRepository->getNotesByUserId($userId);

            return response()->json($notes);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania notatek', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Tworzenie nowej notatki.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            $noteId = $this->noteRepository->saveNote(
                $userId,
                null,
                $validatedData['title'],
                $validatedData['content']
            );

            return response()->json(['status' => 'success', 'message' => 'Notatka została utworzona.', 'note_id' => $noteId]);
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia notatki', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Edycja notatki.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            $noteId = $this->noteRepository->saveNote(
                $userId,
                $id,
                $validatedData['title'],
                $validatedData['content']
            );

            return response()->json(['status' => 'success', 'message' => 'Notatka została zaktualizowana.', 'note_id' => $noteId]);
        } catch (\Exception $e) {
            Log::error('Błąd podczas edycji notatki', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Usuwanie notatki.
     */
    public function destroy($id)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            $result = $this->noteRepository->deleteNoteById($id, $userId);

            if ($result) {
                return response()->json(['status' => 'success', 'message' => 'Notatka została usunięta.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono notatki.'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania notatki', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Udostępnianie notatki innemu użytkownikowi.
     */
    public function share(Request $request, $id)
    {
        $validatedData = $request->validate([
            'shared_with' => 'required|array',
            'shared_with.*' => 'uuid|exists:users,id',
        ]);

        try {
            $this->noteRepository->clearSharedNotes($id);

            foreach ($validatedData['shared_with'] as $userId) {
                $this->noteRepository->shareNoteWithUser($id, $userId);
            }

            return response()->json(['status' => 'success', 'message' => 'Notatka została udostępniona.']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas udostępniania notatki', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Pobieranie współdzielonych notatek.
     */
    public function sharedNotes()
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            $notes = $this->noteRepository->getSharedNotesWithUser($userId);

            return response()->json($notes);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania współdzielonych notatek', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }
}
