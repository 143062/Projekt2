<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log; // Dodano import klasy Log
use App\Repositories\UserRepository;
use App\Repositories\NoteRepository;

class UserController extends Controller
{
    private $userRepository;
    private $noteRepository;

    public function __construct(UserRepository $userRepository, NoteRepository $noteRepository)
    {
        $this->userRepository = $userRepository;
        $this->noteRepository = $noteRepository;
    }

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $login = $request->input('login');
            $password = $request->input('password');
            $user = $this->userRepository->login($login, $password);

            if ($user) {
                Session::put('user_id', $user['id']);
                Session::put('role_id', $user['role_id']);

                $redirectUrl = ($user['role_id'] === $this->getAdminRoleId()) ? '/admin_panel' : '/dashboard';
                return response()->json(['status' => 'success', 'redirect' => $redirectUrl]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Nieprawidłowy login lub hasło']);
            }
        }

        return view('login');
    }

    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $email = $request->input('email');
            $login = $request->input('login');
            $password = $request->input('password');
            $confirmPassword = $request->input('confirm_password');

            if ($password !== $confirmPassword) {
                return response()->json(['status' => 'error', 'message' => 'Hasła nie są zgodne']);
            }

            if (strlen($password) < 6) {
                return response()->json(['status' => 'error', 'message' => 'Utwórz dłuższe hasło']);
            }

            if ($this->userRepository->emailExists($email)) {
                return response()->json(['status' => 'error', 'message' => 'Podany email jest już używany']);
            }

            if ($this->userRepository->loginExists($login)) {
                return response()->json(['status' => 'error', 'message' => 'Podany login jest już używany']);
            }

            if ($this->userRepository->register($email, $login, $password)) {
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Rejestracja nie powiodła się']);
            }
        }

        return view('register');
    }

    public function profile()
    {
        if (!Session::has('user_id')) {
            return redirect('/login');
        }

        if (Session::get('role_id') === $this->getAdminRoleId()) {
            return redirect('/admin_panel');
        }

        $userId = Session::get('user_id');
        $user = $this->userRepository->getUserById($userId);

        return view('profile', ['user' => $user]);
    }

    public function updateProfilePicture(Request $request)
    {
        if (!Session::has('user_id')) {
            return redirect('/login');
        }

        $userId = Session::get('user_id');

        if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
            $file = $request->file('profile_picture');

            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif'])) {
                return response()->json(['success' => false, 'message' => 'Nieprawidłowy format pliku']);
            }

            $uploadPath = public_path('img/profile/' . $userId);

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fileName = 'profile.jpg';
            $filePath = 'img/profile/' . $userId . '/' . $fileName;

            if ($file->move($uploadPath, $fileName)) {
                $this->userRepository->updateProfilePicture($userId, $filePath);
                return response()->json([
                    'success' => true,
                    'newProfilePictureUrl' => '/' . $filePath
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Błąd podczas zapisywania pliku']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Brak pliku do przesłania lub plik jest nieprawidłowy']);
    }

    public function dashboard()
    {
        Log::info('Dashboard: Start metody');
    
        if (!Session::has('user_id')) {
            Log::error('Dashboard: Brak sesji user_id. Przekierowanie na login.');
            return redirect('/login');
        }
    
        $userId = Session::get('user_id');
        Log::info('Dashboard: Pobieranie danych użytkownika dla user_id.', ['user_id' => $userId]);
    
        $user = $this->userRepository->getUserById($userId);
        if (!$user) {
            Log::error('Dashboard: Nie znaleziono danych użytkownika w bazie danych.', ['user_id' => $userId]);
            return redirect('/login');
        }
    
        Log::info('Dashboard: Dane użytkownika zostały pobrane.', ['user' => $user]);
    
        $notes = $this->noteRepository->getNotesByUserId($userId) ?? [];
        Log::info('Dashboard: Pobieranie notatek użytkownika.', ['notes_count' => count($notes)]);
    
        $sharedNotes = $this->noteRepository->getSharedNotesWithUser($userId) ?? [];
        Log::info('Dashboard: Pobieranie współdzielonych notatek.', ['shared_notes_count' => count($sharedNotes)]);
    
        Log::info('Dashboard: Renderowanie widoku dashboard.', [
            'user_id' => $userId,
            'notes_count' => count($notes),
            'shared_notes_count' => count($sharedNotes)
        ]);
    
        return view('dashboard', [
            'user' => $user,
            'notes' => $notes,
            'sharedNotes' => $sharedNotes
        ]);
    }
    
    
    
    

    private function getAdminRoleId()
    {
        return $this->userRepository->getAdminRoleId() ?? 'default-admin-role-id';
    }
}