<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\SharedNote;
use Illuminate\Support\Facades\Log;

class NoteControllerAPI extends Controller
{
    /**
     * Pobieranie listy notatek użytkownika.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $notes = Note::where('user_id', $user->id)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json($notes);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania notatek', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Tworzenie nowej notatki (alias dla storeOrUpdate).
     */
    public function store(Request $request)
    {
        return $this->storeOrUpdate($request);
    }

    /**
     * Tworzenie lub edycja notatki.
     */
/**
 * Tworzenie lub edycja notatki.
 */
public function storeOrUpdate(Request $request, $id = null)
{
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);

    try {
        $user = $request->user();

        if ($id) {
            $note = Note::where('id', $id)->where('user_id', $user->id)->firstOrFail();
            $note->update([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
            ]);
        } else {
            // Tworzenie nowej notatki i przypisanie jej do zmiennej
            $note = Note::create([
                'user_id' => $user->id,
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
            ]);
        }

        // Logujemy ID notatki
        Log::info('Nowa notatka zapisana w bazie', ['note_id' => $note->id]);

        if ($request->has('shared_with')) {
            SharedNote::where('note_id', $note->id)->delete();
            foreach ($request->input('shared_with') as $friendId) {
                SharedNote::create([
                    'note_id' => $note->id,
                    'user_id' => $friendId,
                ]);
            }
        }

        $message = $id ? 'Notatka została zaktualizowana.' : 'Notatka została utworzona.';
        
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'note_id' => $note->id, 
        ]);
    } catch (\Exception $e) {
        Log::error('Błąd podczas tworzenia/edycji notatki', ['error' => $e->getMessage()]);
        return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
    }
}


    /**
     * Usuwanie notatki.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();

            $note = Note::where('id', $id)->where('user_id', $user->id)->firstOrFail();
            $note->delete();

            SharedNote::where('note_id', $id)->delete();

            return response()->json(['status' => 'success', 'message' => 'Notatka została usunięta.']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania notatki', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie znaleziono notatki.'], 404);
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
            SharedNote::where('note_id', $id)->delete();

            foreach ($validatedData['shared_with'] as $userId) {
                SharedNote::create([
                    'note_id' => $id,
                    'user_id' => $userId,
                ]);
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
    public function sharedNotes(Request $request)
    {
        try {
            $user = $request->user();

            $notes = Note::whereHas('sharedWith', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with('user:id,login')
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json($notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'content' => $note->content,
                    'owner_login' => $note->user->login,
                ];
            }));
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania współdzielonych notatek', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Pobieranie użytkowników, którym udostępniono notatkę.
     */
    public function getSharedUsersByNoteId(Request $request, $id)
    {
        try {
            $user = $request->user();

            $note = Note::where('id', $id)->where('user_id', $user->id)->firstOrFail();
            $sharedUsers = SharedNote::where('note_id', $note->id)
                ->with('user:id,login,profile_picture')
                ->get()
                ->map(function ($sharedNote) {
                    return [
                        'id' => $sharedNote->user->id,
                        'login' => $sharedNote->user->login,
                        'profile_picture' => str_replace('public/', '', $sharedNote->user->profile_picture),
                    ];
                });

            return response()->json($sharedUsers);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania użytkowników, którym udostępniono notatkę', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }


    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $note = Note::where('id', $id)
                        ->where('user_id', $user->id) // Pobiera tylko notatki użytkownika
                        ->first();
    
            if (!$note) {
                return response()->json(['message' => 'Notatka nie istnieje lub brak dostępu'], 404);
            }
    
            return response()->json($note); // Zwraca notatkę jako JSON
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania notatki', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }
    






}
