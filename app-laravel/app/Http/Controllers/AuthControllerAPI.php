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
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Rejestracja nowego użytkownika",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "login", "password"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="login", type="string", example="user123"),
     *             @OA\Property(property="password", type="string", example="securepassword"),
     *             @OA\Property(property="password_confirmation", type="string", example="securepassword")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Użytkownik zarejestrowany"),
     *     @OA\Response(response=422, description="Błąd walidacji"),
     *     @OA\Response(response=500, description="Błąd serwera")
     * )
     */
    public function register(Request $request)
    {
        $request->merge([
            'login' => trim($request->login),
            'email' => trim($request->email),
            'password' => trim($request->password),
        ]);

        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            Log::error('Błąd walidacji rejestracji', ['errors' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        // Sprawdzenie, czy rola 'user' istnieje
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            Log::error('Nie znaleziono roli "user" w bazie danych');
            return response()->json([
                'message' => 'Rola "user" nie została znaleziona w bazie danych.',
            ], 500);
        }

        try {
            // Haszowanie hasła
            $hashedPassword = Hash::make($request->password);

            // Tworzenie użytkownika
            $user = User::create([
                'login' => $request->login,
                'email' => $request->email,
                'password' => $hashedPassword,
                'role_id' => $userRole->id,
            ]);

            // Usunięcie wszystkich starych tokenów i wygenerowanie nowego
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('Rejestracja zakończona sukcesem', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Rejestracja zakończona sukcesem',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'login' => $user->login,
                    'email' => $user->email,
                    'role' => $userRole->name,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Błąd podczas rejestracji użytkownika', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Wystąpił błąd podczas rejestracji użytkownika.',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Logowanie użytkownika",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login_or_email", "password"},
     *             @OA\Property(property="login_or_email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="securepassword")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Zalogowano pomyślnie"),
     *     @OA\Response(response=401, description="Nieprawidłowe dane logowania"),
     *     @OA\Response(response=500, description="Błąd serwera")
     * )
     */
    public function login(Request $request)
    {
        $request->merge([
            'login_or_email' => trim($request->login_or_email),
            'password' => trim($request->password),
        ]);

        $validator = Validator::make($request->all(), [
            'login_or_email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Błąd walidacji danych logowania', ['errors' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        $loginOrEmail = $request->login_or_email;
        $isEmail = str_contains($loginOrEmail, '@');
        $user = $isEmail
            ? User::where('email', $loginOrEmail)->with('role')->first()
            : User::where('login', $loginOrEmail)->with('role')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::error('Nieprawidłowe dane logowania', ['login_or_email' => $loginOrEmail]);
            return response()->json(['message' => 'Nieprawidłowe dane logowania.'], 401);
        }

        try {
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('Zalogowano pomyślnie', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Zalogowano pomyślnie',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'login' => $user->login,
                    'email' => $user->email,
                    'role' => $user->role->name,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas generowania tokenu', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return response()->json(['message' => 'Wystąpił błąd podczas logowania.'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Wylogowanie użytkownika",
     *     tags={"Auth"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Wylogowano pomyślnie"),
     *     @OA\Response(response=401, description="Nieautoryzowany")
     * )
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Użytkownik nie jest zalogowany.'], 401);
            }

            $user->tokens()->delete();
            Log::info('Użytkownik wylogowany pomyślnie', ['user_id' => $user->id]);

            Session::flush();

            return response()->json([
                'message' => 'Wylogowano pomyślnie ze wszystkich urządzeń.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas wylogowywania', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Wystąpił błąd podczas wylogowywania.'], 500);
        }
    }


    /**
     * Pobieranie roli aktualnie zalogowanego użytkownika.
     * Endpoint: GET /api/auth/user-role
     */
    public function getUserRole(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Nie jesteś zalogowany.'], 401);
            }

            return response()->json([
                'role' => $user->role->name, // Pobieramy nazwę roli
            ], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania roli użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Wystąpił błąd podczas pobierania roli.'], 500);
        }
    }
}
