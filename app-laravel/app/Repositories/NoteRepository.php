<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class NoteRepository
{
    public function saveNote($userId, $id, $title, $content)
    {
        try {
            if ($id) {
                // Aktualizacja notatki
                DB::table('notes')
                    ->where('id', $id)
                    ->where('user_id', $userId)
                    ->update(['title' => $title, 'content' => $content]);
            } else {
                // Dodanie nowej notatki
                $id = DB::table('notes')->insertGetId([
                    'user_id' => $userId,
                    'title' => $title,
                    'content' => $content,
                ]);
            }
            return $id;
        } catch (\Exception $e) {
            \Log::error("Błąd podczas zapisywania notatki: " . $e->getMessage());
            return false;
        }
    }

    public function clearSharedNotes($noteId)
    {
        try {
            DB::table('shared_notes')->where('note_id', $noteId)->delete();
        } catch (\Exception $e) {
            \Log::error("Błąd podczas czyszczenia udostępnień notatki: " . $e->getMessage());
        }
    }

    public function shareNoteWithUser($noteId, $sharedWithUserId)
    {
        try {
            DB::table('shared_notes')->insert([
                'note_id' => $noteId,
                'shared_with_user_id' => $sharedWithUserId,
            ]);
        } catch (\Exception $e) {
            \Log::error("Błąd podczas udostępniania notatki: " . $e->getMessage());
        }
    }

    public function getNotesByUserId($userId)
    {
        return DB::table('notes')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function getSharedNotesWithUser($userId)
    {
        return DB::table('notes as n')
            ->join('shared_notes as sn', 'n.id', '=', 'sn.note_id')
            ->join('users as u', 'u.id', '=', 'n.user_id')
            ->where('sn.shared_with_user_id', $userId)
            ->select('n.*', 'u.login as owner_login')
            ->orderBy('n.created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function getSharedUsersByNoteId($noteId)
    {
        $sharedUsers = DB::table('shared_notes as sn')
            ->join('users as u', 'sn.shared_with_user_id', '=', 'u.id')
            ->where('sn.note_id', $noteId)
            ->select('u.id', 'u.login', 'u.profile_picture')
            ->get()
            ->toArray();

        // Usuwanie "public/" ze ścieżek zdjęć profilowych
        foreach ($sharedUsers as $user) {
            if (isset($user->profile_picture)) {
                $user->profile_picture = str_replace('public/', '', $user->profile_picture);
            }
        }

        return $sharedUsers;
    }

    public function getNoteById($noteId, $userId)
    {
        return DB::table('notes as n')
            ->leftJoin('shared_notes as sn', 'n.id', '=', 'sn.note_id')
            ->where('n.id', $noteId)
            ->where(function ($query) use ($userId) {
                $query->where('n.user_id', $userId)
                      ->orWhere('sn.shared_with_user_id', $userId);
            })
            ->first();
    }

    public function deleteNoteById($noteId, $userId)
    {
        try {
            // Usunięcie notatki
            $deleted = DB::table('notes')
                ->where('id', $noteId)
                ->where('user_id', $userId)
                ->delete();

            // Usunięcie udostępnień
            if ($deleted) {
                $this->clearSharedNotes($noteId);
            }

            return $deleted > 0;
        } catch (\Exception $e) {
            \Log::error("Błąd podczas usuwania notatki: " . $e->getMessage());
            return false;
        }
    }
}
