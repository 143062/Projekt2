<?php

namespace App\Repositories;

use App\Models\Note;
use App\Models\SharedNote;
use Illuminate\Support\Facades\Log;

class NoteRepository
{
    public function saveNote($userId, $id, $title, $content)
    {
        try {
            // Sprawdzenie duplikatów
            if (!$id && Note::where('user_id', $userId)
                    ->where('title', $title)
                    ->where('content', $content)
                    ->exists()) {
                return false; // Notatka już istnieje
            }

            if ($id) {
                // Aktualizacja notatki
                $note = Note::where('id', $id)
                    ->where('user_id', $userId)
                    ->firstOrFail();
                $note->title = $title;
                $note->content = $content;
                $note->save();
            } else {
                // Dodanie nowej notatki
                $note = Note::create([
                    'user_id' => $userId,
                    'title' => $title,
                    'content' => $content,
                ]);
            }
            return $note->id; // Zwróć UUID notatki
        } catch (\Exception $e) {
            Log::error("Błąd podczas zapisywania notatki: " . $e->getMessage());
            return false;
        }
    }

    public function clearSharedNotes($noteId)
    {
        try {
            SharedNote::where('note_id', $noteId)->delete();
        } catch (\Exception $e) {
            Log::error("Błąd podczas czyszczenia udostępnień notatki: " . $e->getMessage());
        }
    }

    public function shareNoteWithUser($noteId, $sharedWithUserId)
    {
        try {
            SharedNote::create([
                'note_id' => $noteId,
                'user_id' => $sharedWithUserId,
            ]);
        } catch (\Exception $e) {
            Log::error("Błąd podczas udostępniania notatki: " . $e->getMessage());
        }
    }

    public function getNotesByUserId($userId)
    {
        try {
            return Note::where('user_id', $userId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error("Błąd podczas pobierania notatek użytkownika: " . $e->getMessage());
            return [];
        }
    }

    public function getSharedNotesWithUser($userId)
    {
        try {
            return Note::whereHas('sharedWith', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->with('user:id,login') // Pobierz właściciela notatki
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'title' => $note->title,
                        'content' => $note->content,
                        'owner_login' => $note->user->login,
                    ];
                });
        } catch (\Exception $e) {
            Log::error("Błąd podczas pobierania udostępnionych notatek: " . $e->getMessage());
            return [];
        }
    }

    public function getSharedUsersByNoteId($noteId)
    {
        try {
            return SharedNote::where('note_id', $noteId)
                ->with('user:id,login,profile_picture')
                ->get()
                ->map(function ($sharedNote) {
                    return [
                        'id' => $sharedNote->user->id,
                        'login' => $sharedNote->user->login,
                        'profile_picture' => str_replace('public/', '', $sharedNote->user->profile_picture),
                    ];
                });
        } catch (\Exception $e) {
            Log::error("Błąd podczas pobierania użytkowników, którym udostępniono notatkę: " . $e->getMessage());
            return [];
        }
    }

    public function getNoteById($noteId, $userId)
    {
        try {
            return Note::where('id', $noteId)
                ->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                        ->orWhereHas('sharedWith', function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        });
                })
                ->with('sharedWith:id,login')
                ->first();
        } catch (\Exception $e) {
            Log::error("Błąd podczas pobierania szczegółów notatki: " . $e->getMessage());
            return null;
        }
    }

    public function deleteNoteById($noteId, $userId)
    {
        try {
            // Usunięcie notatki
            $note = Note::where('id', $noteId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $note->delete();

            // Usunięcie udostępnień
            $this->clearSharedNotes($noteId);

            return true;
        } catch (\Exception $e) {
            Log::error("Błąd podczas usuwania notatki: " . $e->getMessage());
            return false;
        }
    }
}
