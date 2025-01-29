<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthControllerAPI extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        // Trimowanie danych wejściowych
        $request->merge([
            'login' => trim($request->login),
            'email' => trim($request->email),
            'password' => trim($request->password),
        ]);

        // Validate input data
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            Log::error('Błąd walidacji rejestracji', ['errors' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        try {
            // Pobierz ID roli 'user' z tabeli roles
            $userRole = Role::where('name', 'user')->first();

            if (!$userRole) {
                Log::error('Nie znaleziono roli "user" w bazie danych');
                return response()->json([
                    'message' => 'Rola "user" nie została znaleziona w bazie danych.',
                ], 500);
            }

            // Haszowanie hasła
            $hashedPassword = Hash::make($request->password);
            // Log::info('Zahashowane hasło przed zapisaniem', ['hashed_password' => $hashedPassword]);

            // Create a new user and przypisz domyślną rolę
            $user = User::create([
                'login' => $request->login,
                'email' => $request->email,
                'password' => $hashedPassword, // Używamy zahashowanego hasła
                'role_id' => $userRole->id,
            ]);

            // Generate API token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Log::info('Rejestracja zakończona sukcesem', ['user_id' => $user->id]);
            return response()->json([
                'message' => 'Rejestracja zakończona sukcesem',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Błąd podczas rejestracji użytkownika', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Wystąpił błąd podczas rejestracji użytkownika.',
            ], 500);
        }
    }

    /**
     * Log in a user.
     */
    public function login(Request $request)
    {
        // Trimowanie danych wejściowych
        $request->merge([
            'login_or_email' => trim($request->login_or_email),
            'password' => trim($request->password),
        ]);

        // Log::info('Próba logowania', ['login_or_email' => $request->login_or_email]);

        // Validate login credentials
        $validator = Validator::make($request->all(), [
            'login_or_email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Błąd walidacji danych logowania', ['errors' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        // Określ, czy podano login, czy email
        $loginOrEmail = $request->login_or_email;
        $isEmail = str_contains($loginOrEmail, '@'); // Sprawdzamy obecność "@"

        // Znajdź użytkownika
        $user = $isEmail
            ? User::where('email', $loginOrEmail)->first()
            : User::where('login', $loginOrEmail)->first();

        if (!$user) {
            Log::error('Użytkownik nie został znaleziony', ['login_or_email' => $loginOrEmail]);
            return response()->json(['message' => 'Nieprawidłowe dane logowania.'], 401);
        }

        // Log::info('Hasło zapisane w bazie', ['hashed_password' => $user->password]);

        // Sprawdzenie hasła
        if (!Hash::check($request->password, $user->password)) {
            Log::error('Błędne hasło', ['user_id' => $user->id]);
            return response()->json(['message' => 'Nieprawidłowe dane logowania.'], 401);
        }

        // Generowanie tokenu
        try {
            $token = $user->createToken('auth_token')->plainTextToken;

            // Log::info('Zalogowano pomyślnie', ['user_id' => $user->id]);
            return response()->json([
                'message' => 'Zalogowano pomyślnie',
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas generowania tokenu', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return response()->json(['message' => 'Wystąpił błąd podczas logowania.'], 500);
        }
    }

    /**
     * Log out the authenticated user.
     */
    public function logout(Request $request)
    {
        try {
            // Usunięcie tokenów API użytkownika
            $request->user()->tokens()->delete();
             Log::info('Użytkownik wylogowany pomyślnie', ['user_id' => $request->user()->id]);

            // Zniszczenie wszystkich zmiennych sesji (niepotrzebne po skończeniu miracji, poki co neich sobie bedzie na wszelki wypadek)
            Session::flush();

            return response()->json([
                'message' => 'Wylogowano pomyślnie',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas wylogowywania', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Wystąpił błąd podczas wylogowywania.'], 500);
        }
    }
}
