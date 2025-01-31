<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Note;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;

class UserControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users/me",
     *     summary="Pobieranie profilu zalogowanego użytkownika",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Dane użytkownika"),
     *     @OA\Response(response=401, description="Nieautoryzowany dostęp")
     * )
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        $profilePicture = $user->profile_picture && file_exists(public_path($user->profile_picture)) 
            ? asset($user->profile_picture) 
            : asset('img/profile/default/default_profile_picture.jpg');

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'login' => $user->login,
                'email' => $user->email,
                'profile_picture' => $profilePicture
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/dashboard",
     *     summary="Wyświetlanie dashboardu użytkownika",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Dane użytkownika i jego notatki"),
     *     @OA\Response(response=401, description="Nieautoryzowany dostęp")
     * )
     */
    public function getDashboard(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
        }

        $notes = $user->notes()->get();
        $sharedNotes = Note::whereHas('sharedUsers', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'notes' => $notes,
                'sharedNotes' => $sharedNotes,
            ]
        ], 200);
    }







    /**
     * @OA\Put(
     *     path="/api/users/me",
     *     summary="Aktualizacja profilu użytkownika",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="login", type="string", example="newuser"),
     *             @OA\Property(property="password", type="string", example="newsecurepassword")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profil zaktualizowany pomyślnie"),
     *     @OA\Response(response=500, description="Błąd serwera")
     * )
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $validatedData = $request->validate([
            'email' => 'nullable|email|unique:users,email|max:255',
            'login' => 'nullable|string|unique:users,login|max:100',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            if (isset($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($validatedData);

            return response()->json(['status' => 'success', 'message' => 'Profil zaktualizowany pomyślnie.', 'data' => $user], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas aktualizacji profilu', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Nie udało się zaktualizować profilu.'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/me/profile-picture",
     *     summary="Aktualizacja zdjęcia profilowego użytkownika",
     *     description="Użytkownik może przesłać nowe zdjęcie profilowe. Jeśli miał niestandardowe zdjęcie, stare zostanie usunięte. 
     *                  Domyślne zdjęcie (`default_profile_picture.jpg`) nigdy nie jest usuwane.",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="profile_picture", type="string", format="binary", description="Plik obrazu (jpg, png, gif)")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Zdjęcie profilowe zaktualizowane pomyślnie"),
     *     @OA\Response(response=400, description="Nieprawidłowy format pliku"),
     *     @OA\Response(response=500, description="Błąd serwera")
     * )
     */
    public function updateProfilePicture(Request $request)
    {
        $user = $request->user();
    
        if (!$request->hasFile('profile_picture') || !$request->file('profile_picture')->isValid()) {
            return response()->json(['status' => 'error', 'message' => 'Nie przesłano pliku lub plik jest nieprawidłowy.'], 400);
        }
    
        $file = $request->file('profile_picture');
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            return response()->json(['status' => 'error', 'message' => 'Nieprawidłowy format pliku.'], 400);
        }
    
        $directory = public_path("img/profile/{$user->id}");
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
    
        $fileName = "profile.jpg";
        $filePath = "img/profile/{$user->id}/{$fileName}";
    
        // 📌 Sprawdzenie, czy użytkownik miał ustawione niestandardowe zdjęcie (nie domyślne)
        $defaultProfilePath = "img/profile/default/default_profile_picture.jpg";
        if ($user->profile_picture && file_exists(public_path($user->profile_picture)) && $user->profile_picture !== $defaultProfilePath) {
            @unlink(public_path($user->profile_picture));
        }
    
        try {
            $file->move($directory, $fileName);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Błąd zapisu pliku.'], 500);
        }
    
        $user->update(['profile_picture' => $filePath]);
    
        return response()->json([
            'status' => 'success',
            'message' => 'Zdjęcie profilowe zaktualizowane pomyślnie.',
            'path' => asset($filePath)
        ]);
    }
    
    

    
    /**
     * Rejestracja użytkownika.
     * Endpoint: POST /api/auth/register
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email|max:255',
            'login' => 'required|string|unique:users,login|max:100',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = User::create([
                'email' => $validatedData['email'],
                'login' => $validatedData['login'],
                'password' => Hash::make($validatedData['password']),
                'role_id' => Role::where('name', 'user')->value('id'),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Rejestracja zakończona sukcesem.', 'data' => $user], 201);
        } catch (\Exception $e) {
            Log::error('Błąd podczas rejestracji użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Logowanie użytkownika.
     * Endpoint: POST /api/auth/login
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $user = User::where('login', $validatedData['login'])->first();

            if (!$user || !Hash::check($validatedData['password'], $user->password)) {
                return response()->json(['status' => 'error', 'message' => 'Nieprawidłowy login lub hasło.'], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['status' => 'success', 'message' => 'Logowanie zakończone sukcesem.', 'token' => $token], 200);
        } catch (\Exception $e) {
            Log::error('Błąd podczas logowania użytkownika', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    


}
