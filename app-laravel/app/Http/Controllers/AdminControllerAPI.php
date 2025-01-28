<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AdminControllerAPI extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Pobieranie listy użytkowników.
     * Endpoint: GET /api/admin/users
     */
    public function getUsers()
    {
        $this->authorize('isAdmin'); // Sprawdzenie, czy użytkownik jest adminem

        try {
            $users = $this->userRepository->getAllUsersWithRoles();
            return response()->json(['status' => 'success', 'data' => $users], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania listy użytkowników', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się pobrać listy użytkowników.'], 500);
        }
    }

    /**
     * Dodawanie użytkownika.
     * Endpoint: POST /api/admin/users
     */
    public function addUser(Request $request)
    {
        $this->authorize('isAdmin');

        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email|max:255',
            'login' => 'required|string|unique:users,login|max:100',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:admin,user',
        ]);

        try {
            $this->userRepository->addUser(
                $validatedData['email'],
                $validatedData['login'],
                $validatedData['password'],
                $validatedData['role']
            );

            return response()->json(['status' => 'success', 'message' => 'Użytkownik został dodany.'], 201);
        } catch (\Exception $e) {
            Log::error('Błąd podczas dodawania użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się dodać użytkownika.'], 500);
        }
    }

    /**
     * Usuwanie użytkownika.
     * Endpoint: DELETE /api/admin/users/{id}
     */
    public function deleteUser($id)
    {
        $this->authorize('isAdmin');

        try {
            $this->userRepository->deleteUserById($id);
            return response()->json(['status' => 'success', 'message' => 'Użytkownik został usunięty.'], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się usunąć użytkownika.'], 500);
        }
    }

    /**
     * Resetowanie hasła użytkownika.
     * Endpoint: POST /api/admin/reset-password
     */
    public function resetPassword(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'new_password' => 'required|string|min:6',
        ]);

        try {
            $hashedPassword = Hash::make($validated['new_password']);
            $this->userRepository->updateUserPassword($validated['user_id'], $hashedPassword);

            return response()->json(['status' => 'success', 'message' => 'Hasło użytkownika zostało zresetowane.'], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas resetowania hasła', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się zresetować hasła.'], 500);
        }
    }

    /**
     * Eksport bazy danych.
     * Endpoint: GET /api/admin/sql-dump
     */
    public function exportDatabase()
    {
        $this->authorize('isAdmin');

        try {
            $backupPath = storage_path('app/backup.sql');
            $command = "PGPASSWORD='" . env('DB_PASSWORD') . "' pg_dump -h " . env('DB_HOST') . " -U " . env('DB_USERNAME') . " -d " . env('DB_DATABASE') . " > $backupPath";

            exec($command, $output, $resultCode);

            if ($resultCode === 0) {
                return response()->download($backupPath);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Błąd podczas eksportowania bazy danych.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas eksportowania bazy danych', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się wyeksportować bazy danych.'], 500);
        }
    }

    /**
     * Import bazy danych.
     * Endpoint: POST /api/admin/sql-import
     */
    public function importDatabase(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'sql_file' => 'required|file|mimes:sql',
        ]);

        try {
            $file = $request->file('sql_file');
            $filePath = $file->storeAs('imports', 'import.sql', 'local');

            $command = "PGPASSWORD='" . env('DB_PASSWORD') . "' psql -h " . env('DB_HOST') . " -U " . env('DB_USERNAME') . " -d " . env('DB_DATABASE') . " < " . storage_path("app/$filePath");

            exec($command, $output, $resultCode);

            if ($resultCode === 0) {
                return response()->json(['status' => 'success', 'message' => 'Baza danych została zaimportowana.'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Błąd podczas importowania bazy danych.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas importowania bazy danych', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się zaimportować bazy danych.'], 500);
        }
    }
}
