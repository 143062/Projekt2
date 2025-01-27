<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Pobieranie listy użytkowników (dla admina).
     */
    public function index()
    {
        $users = $this->userRepository->getAllUsersWithRoles();

        return response()->json($users);
    }

    /**
     * Rejestracja użytkownika.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email|max:255',
            'login' => 'required|string|unique:users,login|max:100',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $result = $this->userRepository->register(
                $validatedData['email'],
                $validatedData['login'],
                $validatedData['password']
            );

            if ($result) {
                return response()->json(['status' => 'success', 'message' => 'Rejestracja zakończona sukcesem.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd podczas rejestracji.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas rejestracji użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Logowanie użytkownika.
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $user = $this->userRepository->login(
                $validatedData['login'],
                $validatedData['password']
            );

            if ($user) {
                session(['user_id' => $user['id'], 'role_id' => $user['role_id']]);

                return response()->json(['status' => 'success', 'message' => 'Logowanie zakończone sukcesem.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Nieprawidłowy login lub hasło.'], 401);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas logowania użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Pobranie profilu zalogowanego użytkownika.
     */
    public function profile()
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            $user = $this->userRepository->getUserById($userId);

            if ($user) {
                return response()->json($user);
            }

            return response()->json(['status' => 'error', 'message' => 'Nie znaleziono użytkownika.'], 404);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania profilu użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Aktualizacja profilu użytkownika.
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'nullable|email|unique:users,email|max:255',
            'login' => 'nullable|string|unique:users,login|max:100',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            $updated = $this->userRepository->updateUserProfile($userId, $validatedData);

            if ($updated) {
                return response()->json(['status' => 'success', 'message' => 'Profil zaktualizowany pomyślnie.']);
            }

            return response()->json(['status' => 'error', 'message' => 'Nie udało się zaktualizować profilu.'], 500);
        } catch (\Exception $e) {
            Log::error('Błąd podczas aktualizacji profilu', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Aktualizacja zdjęcia profilowego użytkownika.
     */
    public function updateProfilePicture(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
                $file = $request->file('profile_picture');

                if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif'])) {
                    return response()->json(['status' => 'error', 'message' => 'Nieprawidłowy format pliku.']);
                }

                $fileName = $userId . '_profile.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profiles', $fileName, 'public');

                $this->userRepository->updateProfilePicture($userId, $path);

                return response()->json(['status' => 'success', 'message' => 'Zdjęcie profilowe zaktualizowane pomyślnie.']);
            }

            return response()->json(['status' => 'error', 'message' => 'Nie przesłano pliku lub plik jest nieprawidłowy.']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas aktualizacji zdjęcia profilowego', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }
}
