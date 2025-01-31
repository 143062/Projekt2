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
     * @OA\Get(
     *     path="/api/admin/users",
     *     summary="Pobieranie listy użytkowników",
     *     tags={"Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Lista użytkowników"),
     *     @OA\Response(response=403, description="Brak uprawnień")
     * )
     */
    public function getUsers()
    {
        if ($error = $this->checkAdmin()) return $error;

        try {
            $users = User::with('role')->get();
            return response()->json(['status' => 'success', 'data' => $users], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania listy użytkowników', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się pobrać listy użytkowników.'], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/admin/users",
     *     summary="Dodawanie użytkownika",
     *     tags={"Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "login", "password", "role"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="login", type="string", example="newuser"),
     *             @OA\Property(property="password", type="string", example="securepassword"),
     *             @OA\Property(property="role", type="string", example="user")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Użytkownik został dodany"),
     *     @OA\Response(response=403, description="Brak uprawnień"),
     *     @OA\Response(response=400, description="Nie znaleziono roli")
     * )
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
     * @OA\Delete(
     *     path="/api/admin/users/{id}",
     *     summary="Usuwanie użytkownika",
     *     tags={"Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID użytkownika do usunięcia",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Użytkownik został usunięty"),
     *     @OA\Response(response=403, description="Brak uprawnień"),
     *     @OA\Response(response=404, description="Użytkownik nie istnieje")
     * )
     */
    public function deleteUser($id)
    {
        Log::info("🗑️ Otrzymano żądanie usunięcia użytkownika", ['user_id' => $id]);
    
        if ($error = $this->checkAdmin()) {
            return $error;
        }
    
        try {
            // 📌 Sprawdzenie, czy użytkownik istnieje
            $user = User::find($id);
            if (!$user) {
                Log::error("❌ Użytkownik nie istnieje", ['user_id' => $id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Użytkownik nie istnieje.',
                ], 404);
            }
    
            // 📌 Usuwanie folderu użytkownika z `public/img/profile/`
            $userFolder = public_path("img/profile/$id");
            if (file_exists($userFolder)) {
                $this->deleteDirectory($userFolder);
                Log::info("✅ Usunięto folder użytkownika", ['user_folder' => $userFolder]);
            } else {
                Log::info("ℹ️ Folder użytkownika nie istnieje, pomijam usuwanie", ['user_folder' => $userFolder]);
            }
    
            // 📌 Usuwanie użytkownika
            $user->delete();
            Log::info("✅ Użytkownik usunięty pomyślnie", ['user_id' => $id]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Użytkownik został usunięty.',
            ], 200);
    
        } catch (\Exception $e) {
            Log::error("❌ Błąd podczas usuwania użytkownika", ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Nie udało się usunąć użytkownika.',
            ], 500);
        }
    }
    


/**
 * Usuwa folder użytkownika wraz z jego zawartością.
 */
private function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return;
    }

    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = "$dir/$file";
        if (is_dir($filePath)) {
            $this->deleteDirectory($filePath); // Rekursywne usuwanie podfolderów
        } else {
            unlink($filePath); // Usunięcie pliku
        }
    }

    rmdir($dir); // Usunięcie głównego folderu
}




    /**
     * @OA\Put(
     *     path="/api/admin/users/{id}/password",
     *     summary="Zmiana hasła użytkownika przez administratora",
     *     tags={"Admin"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID użytkownika, którego hasło ma zostać zmienione",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password"},
     *             @OA\Property(property="password", type="string", example="newsecurepassword")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Hasło użytkownika zostało zmienione"),
     *     @OA\Response(response=403, description="Brak uprawnień"),
     *     @OA\Response(response=404, description="Użytkownik nie istnieje")
     * )
     */


     public function changeUserPassword(Request $request, $id)
     {
         Log::info("🔹 Otrzymano żądanie zmiany hasła", ['user_id' => $id]);
     
         if ($error = $this->checkAdmin()) {
             return $error;
         }
     
         try {
             $validatedData = $request->validate([
                 'password' => 'required|string|min:6',
             ]);
         } catch (\Illuminate\Validation\ValidationException $e) {
             Log::error("❌ Błąd walidacji hasła", ['errors' => $e->errors()]);
             return response()->json([
                 'status' => 'error',
                 'message' => 'Błąd walidacji hasła. Hasło musi mieć co najmniej 6 znaków.',
                 'errors' => $e->errors(),
             ], 422);
         }
     
         try {
             $user = User::where('id', $id)->firstOrFail();
             $user->password = Hash::make($validatedData['password']);
             $user->save();
     
             return response()->json([
                 'status' => 'success',
                 'message' => 'Hasło użytkownika zostało zmienione.',
             ], 200);
     
         } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             return response()->json([
                 'status' => 'error',
                 'message' => 'Użytkownik nie istnieje.',
             ], 404);
         } catch (\Exception $e) {
             return response()->json([
                 'status' => 'error',
                 'message' => 'Nie udało się zmienić hasła.',
             ], 500);
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
 * Import bazy danych (nadpisuje całą bazę).
 * Endpoint: POST /api/admin/sql-import
 */
/**
 * Import bazy danych (nadpisuje całą bazę).
 * Endpoint: POST /api/admin/sql-import
 */
public function importDatabase(Request $request)
{
    if ($error = $this->checkAdmin()) return $error;

    try {
        // 📌 Sprawdzenie, czy plik został przesłany
        if (!$request->hasFile('sql_file') || !$request->file('sql_file')->isValid()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nie przesłano poprawnego pliku SQL.',
            ], 400);
        }

        $file = $request->file('sql_file');

        // 📌 Walidacja pliku SQL
        $allowedMimeTypes = ['application/sql', 'text/sql', 'application/octet-stream'];
        $allowedExtensions = ['sql'];

        if (!in_array($file->getMimeType(), $allowedMimeTypes) && !in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nieprawidłowy format pliku. Wybierz plik .sql.',
            ], 400);
        }

        // 📌 Zapisanie pliku SQL na serwerze
        $destinationPath = base_path('database/imports');
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        // 📌 Dynamiczna nazwa pliku, aby uniknąć nadpisywania
        $fileName = 'import_' . now()->format('Ymd_His') . '.sql';
        $file->move($destinationPath, $fileName);
        $importPath = "$destinationPath/$fileName";

        // 📌 **RESET BAZY PRZED IMPORTEM** (usuwa wszystkie tabele)
        $resetCommand = [
            "psql",
            "-h", env('DB_HOST'),
            "-U", env('DB_USERNAME'),
            "-d", env('DB_DATABASE'),
            "-c", "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"
        ];

        $resetProcess = new Process($resetCommand);
        $resetProcess->setTimeout(60);
        $resetProcess->setEnv(["PGPASSWORD" => env('DB_PASSWORD')]);
        $resetProcess->run();

        if (!$resetProcess->isSuccessful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nie udało się zresetować bazy danych przed importem.',
            ], 500);
        }

        // 📌 **Import nowej bazy**
        $importCommand = [
            "psql",
            "-h", env('DB_HOST'),
            "-U", env('DB_USERNAME'),
            "-d", env('DB_DATABASE'),
            "-f", $importPath
        ];

        $importProcess = new Process($importCommand);
        $importProcess->setTimeout(120);
        $importProcess->setEnv(["PGPASSWORD" => env('DB_PASSWORD')]);
        $importProcess->run();

        // 📌 Sprawdzenie, czy import się powiódł
        if (!$importProcess->isSuccessful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nie udało się zaimportować bazy danych.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Baza danych została nadpisana nową bazą i zaimportowana.',
        ], 200);
    } catch (\Exception $e) {
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


















}

