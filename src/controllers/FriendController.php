<?php

namespace App\Controllers;

use App\Repositories\FriendRepository;
use App\Repositories\UserRepository;

class FriendController
{
    private $friendRepository;
    private $userRepository;

    public function __construct()
    {
        $this->friendRepository = new FriendRepository();
        $this->userRepository = new UserRepository();
    }

    public function friends()
    {
        $userId = $_SESSION['user_id'];
        $friends = $this->friendRepository->getFriendsByUserId($userId);
        header('Content-Type: application/json');
        echo json_encode($friends);
        exit();
    }

    public function addFriend()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $friendLogin = $_POST['friend_login'];

            $friend = $this->userRepository->getUserByLogin($friendLogin);

            header('Content-Type: application/json');
            if ($friend) {
                $friendId = $friend['id'];
                $result = $this->friendRepository->addFriend($userId, $friendId);
                
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Znajomy już istnieje na Twojej liście.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Nie znaleziono użytkownika o podanym loginie.']);
            }
            exit();
        }
    }
}
