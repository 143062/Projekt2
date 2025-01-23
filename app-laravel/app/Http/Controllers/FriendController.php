<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\FriendRepository;
use App\Repositories\UserRepository;

class FriendController extends Controller
{
    private $friendRepository;
    private $userRepository;

    public function __construct(FriendRepository $friendRepository, UserRepository $userRepository)
    {
        $this->friendRepository = $friendRepository;
        $this->userRepository = $userRepository;
    }

    public function friends()
    {
        if (!session()->has('user_id')) {
            return redirect('/login');
        }

        $userId = session('user_id');
        $friends = $this->friendRepository->getFriendsByUserId($userId);

        return response()->json($friends);
    }

    public function addFriend(Request $request)
    {
        if ($request->isMethod('post')) {
            if (!session()->has('user_id')) {
                return redirect('/login');
            }

            $userId = session('user_id');
            $friendLogin = trim($request->input('friend_login'));

            if (empty($friendLogin)) {
                return response()->json(['success' => false, 'message' => 'Proszę wpisać login znajomego']);
            }

            $friend = $this->userRepository->getUserByLogin($friendLogin);

            if ($friend) {
                if ($this->userRepository->isAdmin($friend['id'])) {
                    return response()->json(['success' => false, 'message' => 'Nie możesz dodać admina']);
                }

                $friendId = $friend['id'];
                $result = $this->friendRepository->addFriend($userId, $friendId);

                if ($result) {
                    return response()->json(['success' => true]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Znajomy już istnieje na Twojej liście']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Nie znaleziono użytkownika']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Nieprawidłowa metoda HTTP'], 405);
    }

    public function deleteFriend(Request $request)
    {
        if ($request->isMethod('post')) {
            if (!session()->has('user_id')) {
                return redirect('/login');
            }

            $userId = session('user_id');
            $friendLogin = trim($request->input('friend_login'));

            if (empty($friendLogin)) {
                return response()->json(['success' => false, 'message' => 'Login znajomego jest wymagany']);
            }

            $friendId = $this->userRepository->getUserIdByLogin($friendLogin);
            if (!$friendId) {
                return response()->json(['success' => false, 'message' => 'Nie znaleziono znajomego']);
            }

            $result = $this->friendRepository->deleteFriend($userId, $friendId);

            if ($result) {
                return response()->json(['success' => true, 'message' => 'Znajomy został usunięty']);
            } else {
                return response()->json(['success' => false, 'message' => 'Nie udało się usunąć znajomego']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Nieprawidłowa metoda HTTP'], 405);
    }
}
