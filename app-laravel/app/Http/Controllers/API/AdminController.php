<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Pobieranie listy użytkowników.
     */
    public function index()
    {
        try {
            $users = $this->userRepository->getAllUsersWithRoles();
            return response()->json(['status' => 'success', 'data' => $users]);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania listy użytkowników', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się pobrać listy użytkowników.'], 500);
        }
    }

    /**
     * Dodawanie użytkownika.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email|max:255',
            'login' => 'required|string|unique:users,login|max:100',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:admin,user',
        ]);

        try {
            $result = $this->userRepository->addUser(
                $validatedData['email'],
                $validatedData['login'],
                $validatedData['password'],
                $validatedData['role']
            );

            if ($result) {
                return response()->json(['status' => 'success', 'message' => 'Użytkownik został dodany.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Nie udało się dodać użytkownika.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas dodawania użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Usuwanie użytkownika.
     */
    public function destroy($id)
    {
        try {
            $this->userRepository->deleteUserById($id);
            return response()->json(['status' => 'success', 'message' => 'Użytkownik został usunięty.']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się usunąć użytkownika.'], 500);
        }
    }

    /**
     * Eksport bazy danych.
     */
    public function sqlDump()
    {
        try {
            $backupPath = storage_path('app/backup.sql');
            $command = "PGPASSWORD='password' pg_dump -h db -U user -d notatki_db > $backupPath";

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
     */
    public function sqlImport(Request $request)
    {
        $validatedData = $request->validate([
            'sql_file' => 'required|file|mimes:sql',
        ]);

        try {
            $file = $request->file('sql_file');
            $filePath = $file->storeAs('imports', 'import.sql', 'local');

            $command = "PGPASSWORD='password' psql -h db -U user -d notatki_db < " . storage_path("app/$filePath");

            exec($command, $output, $resultCode);

            if ($resultCode === 0) {
                return response()->json(['status' => 'success', 'message' => 'Baza danych została zaimportowana.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Błąd podczas importowania bazy danych.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas importowania bazy danych', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się zaimportować bazy danych.'], 500);
        }
    }
}
