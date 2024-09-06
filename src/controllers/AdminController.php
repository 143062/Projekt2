<?php

namespace App\Controllers;

use App\Repositories\UserRepository;

class AdminController
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function adminPanel()
    {
        session_start();
    
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
            header('Location: /login');
            exit();
        }
    
        $adminRoleId = $this->getAdminRoleId();
    
        if ($_SESSION['role_id'] !== $adminRoleId) {
            echo "Nie masz uprawnień do tej strony.";
            exit();
        }
    
        // Pobranie listy użytkowników z sortowaniem po dacie utworzenia
        $users = $this->userRepository->getAllUsersWithRoles(); // Dodano sortowanie po dacie
    
        include 'public/views/admin_panel.php';
    }
    
    public function deleteUser()
    {
        session_start();

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
            header('Location: /login');
            exit();
        }

        $adminRoleId = $this->getAdminRoleId();

        if ($_SESSION['role_id'] !== $adminRoleId) {
            echo "Nie masz uprawnień do tej akcji.";
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $userId = $_POST['user_id'];
            $this->userRepository->deleteUserById($userId);
            header('Location: /admin_panel');
            exit();
        }
    }

    public function addUser()
    {
        session_start();
    
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
            header('Location: /login');
            exit();
        }
    
        $adminRoleId = $this->getAdminRoleId();
    
        if ($_SESSION['role_id'] !== $adminRoleId) {
            echo "Nie masz uprawnień do tej akcji.";
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $role = $_POST['role'];
    
            $result = $this->userRepository->addUser($email, $username, $password, $role);
    
            if ($result) {
                header('Location: /admin_panel');
                exit();
            } else {
                echo "Wystąpił błąd podczas dodawania użytkownika.";
            }
        }
    }

    public function changePassword()
    {
        session_start();

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
            header('Location: /login');
            exit();
        }

        $adminRoleId = $this->getAdminRoleId();

        if ($_SESSION['role_id'] !== $adminRoleId) {
            echo "Nie masz uprawnień do tej akcji.";
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            $newPassword = $_POST['new_password'];

            // Zaszyfruj nowe hasło
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Zmień hasło w bazie danych
            $this->userRepository->changeUserPassword($userId, $hashedPassword);

            header('Location: /admin_panel?status=password_changed');
            exit();
        }
    }

    private function getAdminRoleId()
    {
        $stmt = $this->userRepository->getPdo()->prepare('SELECT id FROM Roles WHERE name = :role_name');
        $stmt->execute(['role_name' => 'admin']);
        return $stmt->fetchColumn();
    }

    public function resetPassword()
{
    session_start();

    // Sprawdzenie, czy użytkownik jest zalogowany i ma rolę admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
        header('Location: /login');
        exit();
    }

    $adminRoleId = $this->getAdminRoleId();

    if ($_SESSION['role_id'] !== $adminRoleId) {
        echo "Nie masz uprawnień do tej akcji.";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_POST['user_id'];
        $newPassword = $_POST['new_password'];
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $this->userRepository->updateUserPassword($userId, $hashedPassword);
        header('Location: /admin_panel');
        exit();
    }
}


}
