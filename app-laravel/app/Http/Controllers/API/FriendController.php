<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\FriendRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class FriendController extends Controller
{
    private $friendRepository;
    private $userRepository;

    public function __construct(FriendRepository $friendRepository, UserRepository $userRepository)
    {
        $this->friendRepository = $friendRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Pobieranie listy znajomych.
     */
    public function index()
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            $friends = $this->friendRepository->getFriendsByUserId($userId);

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
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            $friend = $this->userRepository->getUserByLogin($validatedData['friend_login']);

            if (!$friend) {
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono użytkownika.'], 404);
            }

            if ($this->userRepository->isAdmin($friend['id'])) {
                return response()->json(['status' => 'error', 'message' => 'Nie możesz dodać administratora jako znajomego.'], 403);
            }

            $result = $this->friendRepository->addFriend($userId, $friend['id']);

            if ($result) {
                return response()->json(['status' => 'success', 'message' => 'Znajomy został dodany.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Znajomy już znajduje się na Twojej liście.']);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas dodawania znajomego', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }

    /**
     * Usuwanie znajomego.
     */
    public function destroy($id)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['status' => 'error', 'message' => 'Nie jesteś zalogowany.'], 401);
            }

            $result = $this->friendRepository->deleteFriend($userId, $id);

            if ($result['rowCount'] > 0) {
                return response()->json(['status' => 'success', 'message' => $result['log']]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Nie znaleziono znajomego na Twojej liście.']);
            }
        } catch (\Exception $e) {
            Log::error('Błąd podczas usuwania znajomego', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Wystąpił błąd.'], 500);
        }
    }
}
