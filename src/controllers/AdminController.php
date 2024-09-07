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

        $users = $this->userRepository->getAllUsersWithRoles();
        include 'public/views/admin_panel.php';
    }

    public function getUsers()
    {
        session_start();

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
            header('Location: /login');
            exit();
        }

        $adminRoleId = $this->getAdminRoleId();

        if ($_SESSION['role_id'] !== $adminRoleId) {
            echo json_encode(['status' => 'error', 'message' => 'Brak dostępu.']);
            exit();
        }

        // Pobranie listy użytkowników
        $users = $this->userRepository->getAllUsersWithRoles();

        // Zwrócenie listy użytkowników jako JSON
        header('Content-Type: application/json');
        echo json_encode($users);
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
            echo json_encode(['status' => 'error', 'message' => 'Brak dostępu.']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $userId = $_POST['user_id'];
            $this->userRepository->deleteUserById($userId);
            echo json_encode(['status' => 'success']);
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
            echo json_encode(['status' => 'error', 'message' => 'Brak dostępu.']);
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $role = $_POST['role'];
    
            $result = $this->userRepository->addUser($email, $username, $password, $role);
    
            if ($result) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Wystąpił błąd podczas dodawania użytkownika.']);
            }
        }
    }
    

    public function sqlDump()
    {
        $databaseDir = '/app/database';
        if (!is_dir($databaseDir)) {
            mkdir($databaseDir, 0777, true);
        }
        $dumpsDir = $databaseDir . '/dumps';
        if (!is_dir($dumpsDir)) {
            mkdir($dumpsDir, 0777, true);
        }
        $backupFile = $dumpsDir . '/backup_' . date('Ymd_His') . '.sql';
        $command = "PGPASSWORD='password' pg_dump -h db -U user -d notatki_db -F c -b -v -f $backupFile";

        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($backupFile) . '"');
            readfile($backupFile);
            exit;
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Wystąpił błąd podczas wykonywania dumpa bazy danych.',
                'details' => $output
            ]);
        }
    }

    public function sqlImport()
    {
        $databaseDir = '/app/database';
        if (!is_dir($databaseDir)) {
            mkdir($databaseDir, 0777, true);
        }

        $importsDir = $databaseDir . '/imports';
        if (!is_dir($importsDir)) {
            mkdir($importsDir, 0777, true);
        }

        if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
            $uploadFile = $importsDir . '/' . basename($_FILES['sql_file']['name']);

            if (move_uploaded_file($_FILES['sql_file']['tmp_name'], $uploadFile)) {
                $command = "PGPASSWORD='password' pg_restore --clean -h db -U user -d notatki_db -v $uploadFile";

                exec($command, $output, $resultCode);

                $logContent = [
                    'command' => $command,
                    'resultCode' => $resultCode,
                    'output' => $output
                ];

                if ($resultCode === 0) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Baza danych została przywrócona pomyślnie.',
                        'logs' => $logContent
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Wystąpił błąd podczas przywracania bazy danych.',
                        'details' => $logContent
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Wystąpił błąd podczas przesyłania pliku.'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Nie wybrano pliku lub wystąpił błąd podczas przesyłania.'
            ]);
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
