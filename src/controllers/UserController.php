<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\NoteRepository;

class UserController
{
    private $userRepository;
    private $noteRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->noteRepository = new NoteRepository();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'];
            $password = $_POST['password'];
            $user = $this->userRepository->login($login, $password);
    
            if ($user) {
                // Ustawienie sesji lub tokenu
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role_id'] = $user['role_id']; // Przechowujemy role_id w sesji
    
                // Sprawdzenie roli użytkownika
                if ($user['role_id'] === $this->getAdminRoleId()) {
                    header('Location: /admin_panel');
                } else {
                    header('Location: /dashboard');
                }
                exit();
            } else {
                echo "Invalid login or password";
            }
        } else {
            include 'public/views/login.php';
        }
    }
    
    // Funkcja pomocnicza do pobierania ID roli admin
    private function getAdminRoleId()
    {
        $stmt = $this->userRepository->getPdo()->prepare('SELECT id FROM Roles WHERE name = :role_name');
        $stmt->execute(['role_name' => 'admin']);
        return $stmt->fetchColumn();
    }
    
    public function register()
    {
        ob_start(); // Rozpoczyna buforowanie wyjścia

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $login = $_POST['login'];
            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['confirm_password']);

            // Sprawdź, czy hasła są takie same
            if ($password === $confirmPassword) {
                $this->userRepository->register($email, $login, $password);
                header('Location: /login');
                exit();
            } else {
                echo "Passwords do not match";
            }
        } else {
            include 'public/views/register.php';
        }

        ob_end_flush(); // Wysyła zawartość bufora i kończy buforowanie
    }

    public function profile()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userRepository->getUserById($userId); // Pobierz dane użytkownika

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle profile update logic
        } else {
            include 'public/views/profile.php';
        }
    }

    public function updateProfilePicture()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
            $profilePicture = $_FILES['profile_picture'];

            if ($profilePicture['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'public/img/';
                $uploadFile = $uploadDir . basename($profilePicture['name']);

                if (move_uploaded_file($profilePicture['tmp_name'], $uploadFile)) {
                    $this->userRepository->updateProfilePicture($userId, $uploadFile);
                    header('Location: /profile?status=updated');
                    exit();
                } else {
                    header('Location: /profile?status=error');
                    exit();
                }
            } else {
                header('Location: /profile?status=error');
                exit();
            }
        }

        header('Location: /profile');
        exit();
    }

    public function dashboard()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userRepository->getUserById($userId); // Pobierz dane użytkownika
        $notes = $this->noteRepository->getNotesByUserId($userId); // Pobierz notatki użytkownika
        $sharedNotes = $this->noteRepository->getSharedNotesWithUser($userId); // Pobierz notatki udostępnione użytkownikowi

        include 'public/views/dashboard.php';
    }
}
