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
        // Validate input data
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Pobierz ID roli 'user' z tabeli roles
            $userRole = Role::where('name', 'user')->first();

            if (!$userRole) {
                return response()->json([
                    'message' => 'Rola "user" nie została znaleziona w bazie danych.',
                ], 500);
            }

            // Create a new user and przypisz domyślną rolę
            $user = User::create([
                'login' => $request->login,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $userRole->id, // Dynamiczne przypisanie roli na podstawie UUID
            ]);

            // Generate API token
            $token = $user->createToken('auth_token')->plainTextToken;

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
        // Validate login credentials
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Nieprawidłowe dane logowania.'], 401);
        }

        // Generate API token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Zalogowano pomyślnie',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    /**
     * Log out the authenticated user.
     */
    public function logout(Request $request)
    {
        // Usunięcie tokenów API użytkownika
        $request->user()->tokens()->delete();

        // Zniszczenie wszystkich zmiennych sesji
        Session::flush();

        return response()->json([
            'message' => 'Wylogowano pomyślnie',
        ], 200);
    }
}
