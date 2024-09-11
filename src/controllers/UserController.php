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

            header('Content-Type: application/json');

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role_id'] = $user['role_id'];

                // Przekierowanie w zależności od roli
                $redirectUrl = ($user['role_id'] === $this->getAdminRoleId()) ? '/admin_panel' : '/dashboard';
                echo json_encode(['status' => 'success', 'redirect' => $redirectUrl]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Nieprawidłowy login lub hasło']);
            }
            exit();
        } else {
            include 'public/views/login.php';
        }
    }

    private function getAdminRoleId()
    {
        $stmt = $this->userRepository->getPdo()->prepare('SELECT id FROM Roles WHERE name = :role_name');
        $stmt->execute(['role_name' => 'admin']);
        return $stmt->fetchColumn();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $login = $_POST['login'];
            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['confirm_password']);
    
            header('Content-Type: application/json'); // Oczekujemy odpowiedzi w formacie JSON
    
            // Sprawdzanie zgodności haseł
            if ($password !== $confirmPassword) {
                echo json_encode(['status' => 'error', 'message' => 'Hasła nie są zgodne']);
                exit();
            }
    
            // Sprawdzanie długości hasła (minimum 6 znaków)
            if (strlen($password) < 6) {
                echo json_encode(['status' => 'error', 'message' => 'Utwórz dłuższe hasło']);
                exit();
            }
    
            if ($this->userRepository->emailExists($email)) {
                echo json_encode(['status' => 'error', 'message' => 'Podany email jest już używany']);
                exit();
            }
    
            if ($this->userRepository->loginExists($login)) {
                echo json_encode(['status' => 'error', 'message' => 'Podany login jest już używany']);
                exit();
            }
    
            if ($this->userRepository->register($email, $login, $password)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Rejestracja nie powiodła się']);
            }
            exit();
        } else {
            include 'public/views/register.php';
        }
    }
    
    
    

    public function profile()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Blokada dla administratora
        if ($_SESSION['role_id'] === $this->getAdminRoleId()) {
            header('Location: /admin_panel');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userRepository->getUserById($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Logika aktualizacji profilu
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
                $uploadDir = 'public/img/profile/' . $userId . '/';
    
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
    
                $uploadFile = $uploadDir . 'profile.jpg';
    
                if (move_uploaded_file($profilePicture['tmp_name'], $uploadFile)) {
                    $this->userRepository->updateProfilePicture($userId, $uploadFile);
                    
                    // Zwracamy odpowiedź JSON zamiast przekierowania
                    echo json_encode([
                        'success' => true,
                        'newProfilePictureUrl' => '/' . $uploadFile // Zwracamy URL nowego zdjęcia
                    ]);
                    exit();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Błąd podczas zapisywania pliku']);
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Błąd podczas przesyłania pliku']);
                exit();
            }
        }
    
        echo json_encode(['success' => false, 'message' => 'Brak pliku do przesłania']);
        exit();
    }
    

    public function dashboard()
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Blokada dla administratora
        if ($_SESSION['role_id'] === $this->getAdminRoleId()) {
            header('Location: /admin_panel');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userRepository->getUserById($userId);
        $notes = $this->noteRepository->getNotesByUserId($userId);
        $sharedNotes = $this->noteRepository->getSharedNotesWithUser($userId);

        include 'public/views/dashboard.php';
    }
}