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

        // 📌 Logujemy ID nowej notatki
        Log::info('Nowa notatka zapisana w bazie', ['note_id' => $note->id]);

        // 📌 Czy obsługa udostępniania działa?
        if ($request->has('shared_with')) {
            Log::info("Udostępnianie notatki", ['note_id' => $note->id, 'shared_with' => $request->input('shared_with')]);
            
            SharedNote::where('note_id', $note->id)->delete(); // Usunięcie starych wpisów
            foreach ($request->input('shared_with') as $friendId) {
                SharedNote::create([
                    'note_id' => $note->id,
                    'user_id' => $friendId,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => $id ? 'Notatka została zaktualizowana.' : 'Notatka została utworzona.',
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
        Log::info('Udostępnianie notatki', [
            'note_id' => $id,
            'shared_with' => $request->input('shared_with')
        ]);
    
        $validatedData = $request->validate([
            'shared_with' => 'required|array',
            'shared_with.*' => 'uuid|exists:users,id',
        ]);
    
        try {
            SharedNote::where('note_id', $id)->delete(); // Usunięcie starych wpisów
    
            foreach ($validatedData['shared_with'] as $userId) {
                SharedNote::create([
                    'note_id' => $id,
                    'user_id' => $userId,
                ]);
            }
    
            Log::info('Notatka została udostępniona poprawnie', [
                'note_id' => $id,
                'users' => $validatedData['shared_with']
            ]);
    
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
                ->with('user:id,login') // Pobieranie właściciela notatki
                ->orderBy('created_at', 'asc')
                ->get();
    
            Log::info('Współdzielone notatki:', ['notes' => $notes]);
    
            return response()->json($notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'content' => $note->content,
                    'owner_login' => $note->user->login, // Dodanie loginu właściciela
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
    
            // Sprawdzenie, czy notatka istnieje i należy do użytkownika
            $note = Note::where('id', $id)->where('user_id', $user->id)->first();
    
            if (!$note) {
                Log::error("Brak dostępu do notatki lub nie istnieje", ['note_id' => $id, 'user_id' => $user->id]);
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono notatki lub brak dostępu.'], 404);
            }
    
            // Pobieranie użytkowników, którym udostępniono notatkę
            $sharedUsers = SharedNote::where('note_id', $note->id)
                ->join('users', 'shared_notes.user_id', '=', 'users.id')
                ->select('users.id', 'users.login', 'users.profile_picture')
                ->get();
    
            // Logowanie do sprawdzenia
            Log::info("Lista użytkowników z dostępem do notatki", ['note_id' => $id, 'users' => $sharedUsers]);
    
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
    
            // Pobierz notatkę jako właściciel
            $note = Note::where('id', $id)
                        ->where('user_id', $user->id)
                        ->first();
    
            // Jeśli użytkownik nie jest właścicielem, sprawdź, czy ma dostęp jako współdzielący
            if (!$note) {
                $note = Note::where('id', $id)
                            ->whereHas('sharedWith', function ($query) use ($user) {
                                $query->where('user_id', $user->id);
                            })
                            ->with('user:id,login') // Pobieramy login właściciela notatki
                            ->first();
            }
    
            if (!$note) {
                return response()->json(['message' => 'Notatka nie istnieje lub brak dostępu'], 404);
            }
    
            return response()->json([
                'id' => $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'owner' => [
                    'id' => $note->user->id,
                    'login' => $note->user->login
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania notatki', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }
    




}
