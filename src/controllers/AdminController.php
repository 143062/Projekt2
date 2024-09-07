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

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userRepository->changeUserPassword($userId, $hashedPassword);

            header('Location: /admin_panel?status=password_changed');
            exit();
        }
    }

    public function sqlDump()
    {
        // Ścieżka do folderu database
        $databaseDir = '/app/database';
    
        // Sprawdzenie, czy folder database istnieje, jeśli nie - utwórz go
        if (!is_dir($databaseDir)) {
            mkdir($databaseDir, 0777, true);
        }
    
        // Ścieżka do folderu dumps
        $dumpsDir = $databaseDir . '/dumps';
    
        // Sprawdzenie, czy folder dumps istnieje, jeśli nie - utwórz go
        if (!is_dir($dumpsDir)) {
            mkdir($dumpsDir, 0777, true);
        }
    
        // Tworzenie pliku dumpa
        $backupFile = $dumpsDir . '/backup_' . date('Ymd_His') . '.sql';
    
        // Komenda pg_dump do tworzenia kopii zapasowej bazy danych
        $command = "PGPASSWORD='password' pg_dump -h db -U user -d notatki_db -F c -b -v -f $backupFile";
    
        // Wykonanie komendy
        exec($command, $output, $resultCode);
    
        if ($resultCode === 0) {
            // Ustawienie nagłówków odpowiedzi do pobierania pliku
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($backupFile) . '"');
            readfile($backupFile);
            exit;
        } else {
            // W przypadku błędu zwróć komunikat o błędzie
            echo json_encode([
                'status' => 'error',
                'message' => 'Wystąpił błąd podczas wykonywania dumpa bazy danych.',
                'details' => $output
            ]);
        }
    }
    
    

    public function sqlImport()
    {
        // Ścieżka do folderu database
        $databaseDir = '/app/database';
    
        // Sprawdzenie, czy folder database istnieje, jeśli nie - utwórz go
        if (!is_dir($databaseDir)) {
            mkdir($databaseDir, 0777, true);
        }
    
        // Ścieżka do folderu imports
        $importsDir = $databaseDir . '/imports';
    
        // Sprawdzenie, czy folder imports istnieje, jeśli nie - utwórz go
        if (!is_dir($importsDir)) {
            mkdir($importsDir, 0777, true);
        }
    
        // Sprawdzenie, czy plik został przesłany
        if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
            $uploadFile = $importsDir . '/' . basename($_FILES['sql_file']['name']);  // Pełna ścieżka do pliku
    
            // Przenieś przesłany plik do folderu imports
            if (move_uploaded_file($_FILES['sql_file']['tmp_name'], $uploadFile)) {
                // Komenda do przywrócenia bazy danych za pomocą pg_restore z opcją --clean
                $command = "PGPASSWORD='password' pg_restore --clean -h db -U user -d notatki_db -v $uploadFile";
    
                // Wykonanie komendy pg_restore
                exec($command, $output, $resultCode);
    
                // Logowanie szczegółowe do odpowiedzi JSON
                $logContent = [
                    'command' => $command,
                    'resultCode' => $resultCode,
                    'output' => $output
                ];
    
                // Sprawdzenie, czy komenda się powiodła
                if ($resultCode === 0) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Baza danych została przywrócona pomyślnie.',
                        'logs' => $logContent
                    ]);
                } else {
                    // Logowanie błędów z pg_restore
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Wystąpił błąd podczas przywracania bazy danych.',
                        'details' => $logContent
                    ]);
                }
            } else {
                // Logowanie błędu przesyłania pliku
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Wystąpił błąd podczas przesyłania pliku.'
                ]);
            }
        } else {
            // Logowanie braku pliku lub błędu przesyłania
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
