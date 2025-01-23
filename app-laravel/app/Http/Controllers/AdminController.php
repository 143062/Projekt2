<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class AdminController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function adminPanel()
    {
        if (!session()->has('user_id') || !session()->has('role_id')) {
            return redirect('/login');
        }

        $adminRoleId = $this->getAdminRoleId();

        if (session('role_id') !== $adminRoleId) {
            return response('Nie masz uprawnień do tej strony.', 403);
        }

        $users = $this->userRepository->getAllUsersWithRoles();
        return view('admin_panel', ['users' => $users]);
    }

    public function getUsers()
    {
        if (!session()->has('user_id') || !session()->has('role_id')) {
            return redirect('/login');
        }

        $adminRoleId = $this->getAdminRoleId();

        if (session('role_id') !== $adminRoleId) {
            return response()->json(['status' => 'error', 'message' => 'Brak dostępu.'], 403);
        }

        $users = $this->userRepository->getAllUsersWithRoles();
        return response()->json($users);
    }

    public function deleteUser(Request $request)
    {
        // Walidacja danych wejściowych
        $validatedData = $request->validate([
            'user_id' => 'required|uuid|exists:users,id', // Upewniamy się, że user_id istnieje i ma format UUID
        ]);
    
        // Sprawdzenie uprawnień
        if (!session()->has('user_id') || !session()->has('role_id')) {
            return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
        }
    
        $adminRoleId = $this->getAdminRoleId();
    
        if (session('role_id') !== $adminRoleId) {
            return response()->json(['status' => 'error', 'message' => 'Brak uprawnień do tej operacji.'], 403);
        }
    
        // Próba usunięcia użytkownika
        try {
            $this->userRepository->deleteUserById($validatedData['user_id']);
            return response()->json(['status' => 'success', 'message' => 'Użytkownik został usunięty.']);
        } catch (\Exception $e) {
            \Log::error('Błąd podczas usuwania użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd podczas usuwania użytkownika.'], 500);
        }
    }
    
    

    public function addUser(Request $request)
    {
        if (!session()->has('user_id') || !session()->has('role_id')) {
            return redirect('/login');
        }

        $adminRoleId = $this->getAdminRoleId();

        if (session('role_id') !== $adminRoleId) {
            return response()->json(['status' => 'error', 'message' => 'Brak dostępu.'], 403);
        }

        if ($request->isMethod('post')) {
            $username = $request->input('username');
            $email = $request->input('email');
            $password = $request->input('password');
            $role = $request->input('role');

            $result = $this->userRepository->addUser($email, $username, $password, $role);

            if ($result) {
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd podczas dodawania użytkownika.']);
            }
        }
    }

    public function sqlDump()
    {
        $databaseDir = base_path('database');
        if (!is_dir($databaseDir)) {
            mkdir($databaseDir, 0777, true);
        }
        $dumpsDir = $databaseDir . '/dumps';
        if (!is_dir($dumpsDir)) {
            mkdir($dumpsDir, 0777, true);
        }
        $backupFile = $dumpsDir . '/backup_' . date('Ymd_His') . '.sql';
        $command = "PGPASSWORD='password' pg_dump -h db -U user -d notatki_db -F c -b -v -f $backupFile";

        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            return response()->download($backupFile);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd podczas wykonywania dumpa bazy danych.', 'details' => $output], 500);
        }
    }

    public function sqlImport(Request $request)
    {
        $databaseDir = base_path('database');
        if (!is_dir($databaseDir)) {
            mkdir($databaseDir, 0777, true);
        }

        $importsDir = $databaseDir . '/imports';
        if (!is_dir($importsDir)) {
            mkdir($importsDir, 0777, true);
        }

        if ($request->hasFile('sql_file')) {
            $file = $request->file('sql_file');
            $uploadFile = $importsDir . '/' . $file->getClientOriginalName();

            $file->move($importsDir, $file->getClientOriginalName());

            $command = "PGPASSWORD='password' pg_restore --clean -h db -U user -d notatki_db -v $uploadFile";

            exec($command, $output, $resultCode);

            if ($resultCode === 0) {
                return response()->json(['status' => 'success', 'message' => 'Baza danych została przywrócona pomyślnie.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd podczas przywracania bazy danych.', 'details' => $output], 500);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Nie wybrano pliku lub wystąpił błąd podczas przesyłania.'], 400);
        }
    }

    public function resetPassword(Request $request)
    {
        if (!session()->has('user_id') || !session()->has('role_id')) {
            return redirect('/login');
        }

        $adminRoleId = $this->getAdminRoleId();

        if (session('role_id') !== $adminRoleId) {
            return response('Nie masz uprawnień do tej akcji.', 403);
        }

        if ($request->isMethod('post')) {
            $userId = $request->input('user_id');
            $newPassword = $request->input('new_password');
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $this->userRepository->updateUserPassword($userId, $hashedPassword);
            return redirect('/admin_panel');
        }
    }

    private function getAdminRoleId()
    {
        return $this->userRepository->getAdminRoleId();
    }
}
