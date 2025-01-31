<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FriendControllerAPI extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/friends",
     *     summary="Pobieranie listy znajomych",
     *     tags={"Friends"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Lista znajomych"),
     *     @OA\Response(response=500, description="Błąd serwera")
     * )
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
     * @OA\Post(
     *     path="/api/friends",
     *     summary="Dodawanie znajomego",
     *     tags={"Friends"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"friend_login"},
     *             @OA\Property(property="friend_login", type="string", example="friendUser")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Znajomy został dodany"),
     *     @OA\Response(response=400, description="Nie można dodać siebie jako znajomego"),
     *     @OA\Response(response=403, description="Nie można dodać administratora"),
     *     @OA\Response(response=500, description="Błąd serwera")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'friend_login' => 'required|string|exists:users,login',
        ]);

        try {
            $user = $request->user();
            $friend = User::where('login', $validatedData['friend_login'])->first();

            // Nie można dodać samego siebie
            if ($user->id === $friend->id) {
                return response()->json(['status' => 'error', 'message' => 'Nie możesz dodać siebie jako znajomego.'], 400);
            }

            // Nie można dodać administratora jako znajomego
            if ($friend->role->name === 'admin') {
                return response()->json(['status' => 'error', 'message' => 'Nie możesz dodać administratora jako znajomego.'], 403);
            }

            // Sprawdzenie, czy znajomy już istnieje w pivot table
            if ($user->friends()->wherePivot('friend_id', $friend->id)->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Znajomy już znajduje się na Twojej liście.']);
            }

            // Dodanie znajomego
            $user->friends()->attach($friend->id);

            return response()->json(['status' => 'success', 'message' => 'Znajomy został dodany.']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas dodawania znajomego', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/friends/{id}",
     *     summary="Usuwanie znajomego",
     *     tags={"Friends"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID znajomego do usunięcia",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Znajomy został usunięty"),
     *     @OA\Response(response=404, description="Nie znaleziono użytkownika"),
     *     @OA\Response(response=500, description="Błąd serwera")
     * )
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Najpierw sprawdź, czy użytkownik o danym ID istnieje
            if (!User::where('id', $id)->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono użytkownika.'], 404);
            }

            // Sprawdzenie znajomości w pivot table
            if (!$user->friends()->wherePivot('friend_id', $id)->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono znajomego na Twojej liście.']);
            }

            // Usunięcie znajomego
            $user->friends()->detach($id);

            return response()->json(['status' => 'success', 'message' => 'Znajomy został usunięty.']);
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania znajomego', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }
}
