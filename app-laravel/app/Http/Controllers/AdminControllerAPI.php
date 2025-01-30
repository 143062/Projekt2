<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AdminControllerAPI extends Controller
{
    /**
     * Sprawdza, czy użytkownik jest administratorem.
     */
    private function checkAdmin()
    {
        if (!auth()->check() || auth()->user()->role->name !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Brak uprawnień.'], 403);
        }
        return null;
    }

    /**
     * Pobieranie listy użytkowników.
     * Endpoint: GET /api/admin/users
     */
    public function getUsers()
    {
        if ($error = $this->checkAdmin()) return $error;

        try {
            $users = User::with('role')->get(); // Pobieranie użytkowników z relacją do ról
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
        if ($error = $this->checkAdmin()) return $error;

        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email|max:255',
            'login' => 'required|string|unique:users,login|max:100',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,user',
        ]);

        try {
            $role = Role::where('name', $validatedData['role'])->first();
            if (!$role) {
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono roli.'], 400);
            }

            User::create([
                'email' => $validatedData['email'],
                'login' => $validatedData['login'],
                'password' => Hash::make($validatedData['password']),
                'role_id' => $role->id,
            ]);

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
        if ($error = $this->checkAdmin()) return $error;

        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Użytkownik nie istnieje.'], 404);
            }

            $user->delete();
            return response()->json(['status' => 'success', 'message' => 'Użytkownik został usunięty.'], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się usunąć użytkownika.'], 500);
        }
    }

            /**
             * Eksport bazy danych.
             * Endpoint: GET /api/admin/sql-dump
             */
            public function exportDatabase()
            {
                if ($error = $this->checkAdmin()) return $error;

                try {
                    $backupDir = base_path('database/dumps'); // Katalog dla dumpów
                    if (!is_dir($backupDir)) {
                        mkdir($backupDir, 0777, true); // Tworzenie katalogu, jeśli nie istnieje
                    }

                    $timestamp = now()->format('Y-m-d_H-i-s'); // Aktualna data i czas
                    $backupFileName = "backup_$timestamp.sql"; // Dynamiczna nazwa pliku
                    $backupPath = "$backupDir/$backupFileName"; // Pełna ścieżka pliku
                    $command = "PGPASSWORD='" . env('DB_PASSWORD') . "' pg_dump -h " . env('DB_HOST') . " -U " . env('DB_USERNAME') . " -d " . env('DB_DATABASE') . " > $backupPath";

                    exec($command, $output, $resultCode);

                    if ($resultCode !== 0) {
                        Log::error("Błąd eksportowania bazy danych", ['output' => implode("\n", $output)]);
                        return response()->json(['status' => 'error', 'message' => 'Nie udało się wyeksportować bazy danych.'], 500);
                    }

                    return response()->download($backupPath, $backupFileName);
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
    if ($error = $this->checkAdmin()) return $error;

    $validated = $request->validate([
        'sql_file' => 'required|file|mimes:sql',
    ]);

    try {
        // 📌 **Zapisujemy plik do `database/imports/`**
        $destinationPath = base_path('database/imports'); // Folder do przechowywania plików importu
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true); // Tworzenie katalogu, jeśli nie istnieje
        }

        $fileName = 'import.sql'; // Możemy użyć dynamicznej nazwy np. `import_YYYY-MM-DD_HH-MM-SS.sql`
        $file->move($destinationPath, $fileName);

        $importPath = "$destinationPath/$fileName"; // Pełna ścieżka do importowanego pliku

        // 📌 **Komenda do importu SQL**
        $command = [
            "psql",
            "-h", env('DB_HOST'),
            "-U", env('DB_USERNAME'),
            "-d", env('DB_DATABASE'),
            "-f", $importPath
        ];

        $process = new Process($command);
        $process->setTimeout(120); // ⏳ Limit czasu na 2 minuty
        $process->setEnv(["PGPASSWORD" => env('DB_PASSWORD')]);

        $process->run();

        // 📌 **Logowanie wyników procesu**
        Log::info('Import SQL - Output:', ['output' => $process->getOutput()]);
        Log::info('Import SQL - Error Output:', ['error_output' => $process->getErrorOutput()]);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Baza danych została zaimportowana.',
        ], 200);

    } catch (ProcessFailedException $e) {
        Log::error('Błąd w procesie importu SQL', [
            'error' => $e->getMessage(),
            'process_output' => $process->getOutput(),
            'process_error' => $process->getErrorOutput(),
        ]);
        return response()->json([
            'status' => 'error',
            'message' => 'Nie udało się zaimportować bazy danych. Sprawdź logi.',
        ], 500);

    } catch (\Exception $e) {
        Log::error('Błąd podczas importowania bazy danych', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'message' => 'Nie udało się zaimportować bazy danych.',
        ], 500);
    }
}



    /**
     * Uruchamianie testów jednostkowych.
     * Endpoint: POST /api/admin/run-tests
     */
    public function runTests()
    {
        if ($error = $this->checkAdmin()) return $error;

        try {
            $output = shell_exec("php artisan test --parallel");
            return response()->json(['status' => 'success', 'output' => $output], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas uruchamiania testów', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się uruchomić testów.'], 500);
        }
    }





/**
 * Zmiana hasła użytkownika przez administratora.
 * Endpoint: PUT /api/admin/users/{id}/password
 */
public function changeUserPassword(Request $request, $id)
{
    if ($error = $this->checkAdmin()) return $error;

    $validatedData = $request->validate([
        'password' => 'required|string|min:6',
    ]);

    try {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Użytkownik nie istnieje.'], 404);
        }

        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Hasło użytkownika zostało zmienione.'], 200);
    } catch (\Exception $e) {
        Log::error('Błąd podczas zmiany hasła użytkownika', ['error' => $e->getMessage()]);
        return response()->json(['status' => 'error', 'message' => 'Nie udało się zmienić hasła.'], 500);
    }
}






















}


