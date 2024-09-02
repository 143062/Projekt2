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

        // Sprawdzenie, czy użytkownik jest zalogowany i ma rolę admin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
            header('Location: /login');
            exit();
        }

        $adminRoleId = $this->getAdminRoleId();

        if ($_SESSION['role_id'] !== $adminRoleId) {
            echo "Nie masz uprawnień do tej strony.";
            exit();
        }

        // Pobranie listy użytkowników z bazy danych
        $users = $this->userRepository->getAllUsers();

        // Przekazanie danych do widoku
        include 'public/views/admin_panel.php';
    }

    public function deleteUser()
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
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Wywołanie metody addUser w UserRepository
            $result = $this->userRepository->addUser($email, $username, $password);

            if ($result) {
                // Przekierowanie z powrotem do panelu admina
                header('Location: /admin_panel');
                exit();
            } else {
                echo "Wystąpił błąd podczas dodawania użytkownika.";
            }
        }
    }

    private function getAdminRoleId()
    {
        $stmt = $this->userRepository->getPdo()->prepare('SELECT id FROM Roles WHERE name = :role_name');
        $stmt->execute(['role_name' => 'admin']);
        return $stmt->fetchColumn();
    }
}
