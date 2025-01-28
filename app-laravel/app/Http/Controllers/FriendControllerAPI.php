<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FriendControllerAPI extends Controller
{
    /**
     * Pobieranie listy znajomych.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user(); // Pobranie użytkownika z tokenu Sanctum
            $friends = $user->friends()->select('id', 'login', 'email', 'profile_picture')->get();

            // Usunięcie "public/" ze ścieżek zdjęć profilowych znajomych
            $friends = $friends->map(function ($friend) {
                if ($friend->profile_picture) {
                    $friend->profile_picture = str_replace('public/', '', $friend->profile_picture);
                }
                return $friend;
            });

            return response()->json($friends);
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania listy znajomych', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Dodawanie znajomego.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'friend_login' => 'required|string',
        ]);

        try {
            $user = $request->user(); // Pobranie użytkownika z tokenu Sanctum

            $friend = User::where('login', $validatedData['friend_login'])->first();

            if (!$friend) {
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono użytkownika.'], 404);
            }

            if ($friend->role->name === 'admin') {
                return response()->json(['status' => 'error', 'message' => 'Nie możesz dodać administratora jako znajomego.'], 403);
            }

            if ($user->friends()->where('friend_id', $friend->id)->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Znajomy już znajduje się na Twojej liście.']);
            }

            $user->friends()->attach($friend->id);

            return response()->json(['status' => 'success', 'message' => 'Znajomy został dodany.']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas dodawania znajomego', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Usuwanie znajomego.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user(); // Pobranie użytkownika z tokenu Sanctum

            if (!$user->friends()->where('friend_id', $id)->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono znajomego na Twojej liście.']);
            }

            $user->friends()->detach($id);

            return response()->json(['status' => 'success', 'message' => 'Znajomy został usunięty.']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania znajomego', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }
}
