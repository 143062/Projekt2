<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use App\Tests\TestHelper;
use PDO;
use PDOException;

require_once __DIR__ . '/../src/repositories/UserRepository.php';
require_once __DIR__ . '/TestHelper.php';

class UserRepositoryTest extends TestCase
{
    private static $pdo;
    private static $userRepository;
    private static $userId;
    private static $newUserId;  // Przechowywanie UUID nowego użytkownika
    private static $additionalUsers = [];  // Przechowywanie dodatkowych użytkowników do usunięcia

    public static function setUpBeforeClass(): void
    {
        try {
            // Pobieranie połączenia z bazą danych przez TestHelper
            self::$pdo = TestHelper::getPdo();
            self::$userRepository = new UserRepository(self::$pdo);

            // Generowanie UUID dla testowego użytkownika
            self::$userId = TestHelper::generateUuid();

            // Dodanie testowego użytkownika, jeśli nie istnieje
            TestHelper::addTestUserIfNotExists(self::$pdo, self::$userId, 'test_user', 'test_user@example.com');
        } catch (PDOException $e) {
            self::fail("Nie udało się połączyć z bazą danych: " . $e->getMessage());
        }
    }

    public static function tearDownAfterClass(): void
    {
        // Usuwanie testowego użytkownika
        TestHelper::deleteTestUser(self::$pdo, self::$userId);
        echo "Użytkownik o ID: " . self::$userId . " został usunięty z bazy danych.\n";

        // Usuwanie nowo zarejestrowanego użytkownika
        if (self::$newUserId) {
            TestHelper::deleteTestUser(self::$pdo, self::$newUserId);
            echo "Użytkownik o ID: " . self::$newUserId . " (new_test_user) został usunięty z bazy danych.\n";
        }

        // Usuwanie dodatkowych użytkowników
        foreach (self::$additionalUsers as $additionalUserId) {
            TestHelper::deleteTestUser(self::$pdo, $additionalUserId);
            echo "Dodatkowy użytkownik o ID: $additionalUserId został usunięty z bazy danych.\n";
        }
    }

    // Testy rejestracji
    public function testRegisterUser(): void
    {
        $login = 'new_test_user';
        $email = 'new_test_user@example.com';
        $password = 'password123';

        // Rejestracja nowego użytkownika
        $result = self::$userRepository->register($email, $login, $password);

        // Pobranie nowo zarejestrowanego użytkownika i zapisanie jego ID
        $newUser = self::$userRepository->getUserByLogin($login);
        self::$newUserId = $newUser['id'];

        // Sprawdzenie, czy użytkownik został poprawnie zarejestrowany
        $this->assertTrue($result, 'Nowy użytkownik powinien zostać zarejestrowany.');
    }

    // Testy logowania
    public function testLoginUser(): void
    {
        $login = 'test_user';
        $password = 'password';

        // Próba zalogowania użytkownika
        $result = self::$userRepository->login($login, $password);
        $this->assertNotFalse($result, 'Użytkownik powinien się poprawnie zalogować.');
    }

    // Testy pobierania użytkownika po loginie
    public function testGetUserByLogin(): void
    {
        $login = 'test_user';

        // Pobranie użytkownika z bazy na podstawie loginu
        $user = self::$userRepository->getUserByLogin($login);
        $this->assertNotNull($user, 'Powinien zostać zwrócony użytkownik o podanym loginie.');
    }

    // Test sprawdzający, czy e-mail już istnieje
    public function testEmailExists(): void
    {
        $email = 'test_user@example.com';
        $exists = self::$userRepository->emailExists($email);
        $this->assertTrue($exists, 'Email powinien istnieć w bazie danych.');
    }

    // Test sprawdzający, czy login już istnieje
    public function testLoginExists(): void
    {
        $login = 'test_user';
        $exists = self::$userRepository->loginExists($login);
        $this->assertTrue($exists, 'Login powinien istnieć w bazie danych.');
    }

    // Test dodawania nowego użytkownika
    public function testAddUser(): void
    {
        $email = 'test2@example.com';
        $login = 'test_user_2';
        $password = 'password';
        $role = 'user';

        // Rejestracja nowego użytkownika
        $result = self::$userRepository->addUser($email, $login, $password, $role);

        // Pobranie ID nowo zarejestrowanego użytkownika i zapisanie go do dodatkowych użytkowników
        $newUser = self::$userRepository->getUserByLogin($login);
        self::$additionalUsers[] = $newUser['id'];

        $this->assertTrue($result, 'Użytkownik powinien zostać dodany poprawnie.');
    }

    // Test pobierania wszystkich użytkowników z rolami
    public function testGetAllUsersWithRoles(): void
    {
        $users = self::$userRepository->getAllUsersWithRoles();
        $this->assertNotEmpty($users, 'Powinno być zwrócone co najmniej kilku użytkowników z rolami.');
    }

    // Test aktualizacji zdjęcia profilowego użytkownika
    public function testUpdateProfilePicture(): void
    {
        $profilePicturePath = 'path/to/new/profile_picture.jpg';
        self::$userRepository->updateProfilePicture(self::$userId, $profilePicturePath);

        $user = self::$userRepository->getUserById(self::$userId);
        $this->assertEquals($profilePicturePath, $user['profile_picture'], 'Ścieżka do zdjęcia profilowego powinna być zaktualizowana.');
    }

    // Test sprawdzenia, czy użytkownik jest administratorem
    public function testIsAdmin(): void
    {
        $isAdmin = self::$userRepository->isAdmin(self::$userId);
        $this->assertFalse($isAdmin, 'Użytkownik nie powinien mieć roli administratora.');
    }

    // Test zmiany hasła użytkownika
    public function testChangeUserPassword(): void
    {
        $newPassword = password_hash('new_password', PASSWORD_BCRYPT);
        self::$userRepository->changeUserPassword(self::$userId, $newPassword);

        $user = self::$userRepository->getUserByLogin('test_user');
        $this->assertTrue(password_verify('new_password', $user['password']), 'Hasło powinno być zmienione.');
    }

    // Test usuwania użytkownika
    public function testDeleteUserById(): void
    {
        self::$userRepository->deleteUserById(self::$newUserId);
        $user = self::$userRepository->getUserById(self::$newUserId);
        $this->assertFalse($user, 'Użytkownik powinien zostać usunięty.');
    }
}
