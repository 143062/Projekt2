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
            $user = $request->user();
            $friends = $user->friends()->select('id', 'login', 'email', 'profile_picture')->get();

            // Usunięcie "public/" ze ścieżek zdjęć profilowych znajomych
            $friends->transform(function ($friend) {
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
            'friend_login' => 'required|string|exists:users,login',
        ]);

        try {
            $user = $request->user();
            $friend = User::where('login', $validatedData['friend_login'])->first();

            //  Nie można dodać samego siebie
            if ($user->id === $friend->id) {
                return response()->json(['status' => 'error', 'message' => 'Nie możesz dodać siebie jako znajomego.'], 400);
            }

            //  Nie można dodać administratora jako znajomego
            if ($friend->role->name === 'admin') {
                return response()->json(['status' => 'error', 'message' => 'Nie możesz dodać administratora jako znajomego.'], 403);
            }

            //  Sprawdzenie, czy znajomy już istnieje w pivot table
            if ($user->friends()->wherePivot('friend_id', $friend->id)->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Znajomy już znajduje się na Twojej liście.']);
            }

            // ✅ Dodanie znajomego
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
            $user = $request->user();

            //  Najpierw sprawdź, czy użytkownik o danym ID istnieje
            if (!User::where('id', $id)->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono użytkownika.'], 404);
            }

            //  Sprawdzenie znajomości w pivot table
            if (!$user->friends()->wherePivot('friend_id', $id)->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono znajomego na Twojej liście.']);
            }

            // ✅ Usunięcie znajomego
            $user->friends()->detach($id);

            return response()->json(['status' => 'success', 'message' => 'Znajomy został usunięty.']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania znajomego', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }
}
